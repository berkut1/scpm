<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\AllinOne\Create\VM;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\Package\PackageStatus;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package as SOAPPackage;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create as SOAPUserCreate;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012 as SOAPVirtualizationServer2012;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackageRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsServers;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;
use JetBrains\PhpStorm\ArrayShape;

class Handler
{
    private EnterpriseDispatcher $enterpriseDispatcher;
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private VirtualMachinePackageRepository $virtualMachinePackageRepository;
    private SOAPVirtualizationServer2012\AvailableSpace\Handler $soapVpsAvailableSpaceHandler;
    private SOAPUserCreate\Handler $soapUserCreateHandler;
    private SOAPPackage\Create\Handler $soapCreatePackageHandler;
    private SOAPVirtualizationServer2012\CreateVM\Handler $soapCreateVmHandler;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(
        EnterpriseDispatcherRepository                      $enterpriseDispatcherRepository,
        VirtualMachinePackageRepository                     $virtualMachinePackageRepository,
        SOAPVirtualizationServer2012\AvailableSpace\Handler $soapVpsAvailableSpaceHandler,
        SOAPUserCreate\Handler                              $soapUserCreateHandler,
        SOAPPackage\Create\Handler                          $soapCreatePackageHandler,
        SOAPVirtualizationServer2012\CreateVM\Handler       $soapCreateVmHandler,
        AuditLog\Add\SolidCP\Handler                        $auditLogHandler
    )
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->virtualMachinePackageRepository = $virtualMachinePackageRepository;
        $this->soapVpsAvailableSpaceHandler = $soapVpsAvailableSpaceHandler;
        $this->soapUserCreateHandler = $soapUserCreateHandler;
        $this->soapCreatePackageHandler = $soapCreatePackageHandler;
        $this->soapCreateVmHandler = $soapCreateVmHandler;
        $this->auditLogHandler = $auditLogHandler;
    }

    /**
     * @throws \Exception
     */
    #[ArrayShape(['is_user_exists' => "bool", 'solidcp_package_id' => "int", 'vps' => "array", 'solidcp_server_node' => "array"])]
    public function handle(Command $command): array
    {
        if ($command->server_ip_amount < 1) {
            throw new \DomainException("The server must have at least one IP");
        }
        $this->enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        if(!$this->enterpriseDispatcher->isEnabled()){
            throw new \DomainException("The EnterpriseDispatcher {$this->enterpriseDispatcher->getName()} is disabled");
        }

        /** @var Record[] $records */
        $records = [];

        $commandPossibleSpace = SOAPVirtualizationServer2012\AvailableSpace\Command::create(
            $command->server_location_name,
            $command->server_package_name,
            $command->server_ip_amount,
            $command->ignore_node_ids,
            $command->ignore_hosting_space_ids,
            $this->enterpriseDispatcher->getId());
        $possibleSpaces = $this->soapVpsAvailableSpaceHandler->handle($commandPossibleSpace, $records, false);
        if(count($possibleSpaces) === 0){
            $this->saveAuditLogAndThrowDomainException($records, "No free spaces for VMs or was not assigned plants to VM packages");
        }


        //get a random space from $possibleSpace
        $maxIdxVal = count($possibleSpaces) - 1;
        $possibleSpace = $possibleSpaces[random_int(0, $maxIdxVal)];
        $records[] = Record::create('SOLIDCP_SELECTED_POSSIBLE_SPACE_NAME_AND_ID', [
                $possibleSpace->getName(),
                $possibleSpace->getSolidCpIdHostingSpace()
            ]);

        if (!$osTemplate = $possibleSpace->getOsTemplateByName($command->server_os_name)) {
            $this->saveAuditLogAndThrowDomainException($records, "The OS $command->server_os_name was not found");
        }

        $esUsers = EsUsers::createFromEnterpriseDispatcher($this->enterpriseDispatcher);
        $userId = 0;
        if (!$userExists = $esUsers->userExists($command->client_login)) {
            $commandUser = SOAPUserCreate\Command::create(
                $command->client_login,
                null,
                null,
                $command->client_email,
                $command->client_password,
                $this->enterpriseDispatcher->getId()
            );
            $userId = $this->soapUserCreateHandler->handle($commandUser, $records, false);
        } else {
            $user = $esUsers->getUserByUsername($command->client_login);
            if ($user['StatusId'] !== 1) {
                $this->saveAuditLogAndThrowDomainException($records, "The client account is suspended/canceled");
            }
            $userId = $user['UserId'];
            $records[] = Record::create('SOLIDCP_TOOK_EXISTED_USER_WITH_ID', [
                $userId
            ]);
        }

        $commandPackage = SOAPPackage\Create\Command::create(
            $userId,
            $possibleSpace->getDefaultHostingPlan()->getSolidcpIdPlan() ?? 0, //if 0 get exception
            null,
            $this->enterpriseDispatcher->getId()
        );
        $packageId = $this->soapCreatePackageHandler->handle($commandPackage, $records, false);

        $esServers = EsServers::createFromEnterpriseDispatcher($this->enterpriseDispatcher);
        $result = $esServers->allocatePackageIPAddressesVpsExternalNetwork($packageId, true, $command->server_ip_amount);
        $esPackages = EsPackages::createFromEnterpriseDispatcher($this->enterpriseDispatcher);
        $this->guardResultAndRenameProblemPackage($result, $esPackages, $packageId, $possibleSpace->getDefaultHostingPlan()->getName(), $records);
        $records[] = Record::create('SOLIDCP_ASSIGNED_IP_AMOUNT_TO_PACKAGE_ID', [$command->server_ip_amount, $packageId]);

        $package = $this->virtualMachinePackageRepository->getByName($command->server_package_name);
        $commandVm = SOAPVirtualizationServer2012\CreateVM\Command::createDefault(
            $packageId,
            $package->getId()->getValue(),
            $osTemplate->getPath(),
            $command->server_password,
            $command->server_ip_amount,
            $this->enterpriseDispatcher->getId()
        );
        $vmResultArray = $this->soapCreateVmHandler->handle($commandVm, $records, false);
        $this->guardResultAndRenameProblemPackage($vmResultArray, $esPackages, $packageId, $possibleSpace->getDefaultHostingPlan()->getName(), $records);


        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($this->enterpriseDispatcher);
        $vmItemResult = $esVirtualizationServer2012->getVirtualMachines($packageId)['Items']['VirtualMachineMetaItem'];
        if ($vmItemResult['ItemID'] !== $vmResultArray['Value']) { //check that we get correct item, maybe useless check, but with SolidCP we can't be sure
            $newItemIDResult = $vmResultArray['Value'];
            $getItemIDResult = $vmItemResult['ItemID'];
            $this->saveAuditLogAndThrowDomainException($records, "Something wrong with ItemID, should be $newItemIDResult, but got $getItemIDResult");
        }
        //rename package to ip
        $pieces = explode(".", $vmItemResult['ItemName']); //ItemName is hostname name.domain.local
        $esPackages->updatePackageName($packageId, $pieces[0]);
        $records[] = Record::create('SOLIDCP_RENAMED_PACKAGE_WITH_ID_TO_NAME', [$packageId, $pieces[0]]);

        $resultPackageIPs = $esServers->getPackageIPAddressesVpsExternalNetwork($packageId)['Items'];
        $mainIp = '';
        if($command->server_ip_amount === 1){
            $mainIp = $resultPackageIPs['PackageIPAddress']['ExternalIP'];
        }

        $filteredResultPackageSecondaryIPs = [];
        if ($command->server_ip_amount > 1) {
            foreach ($resultPackageIPs['PackageIPAddress'] as $one) {
                if ($one['IsPrimary']) {
                    $mainIp = $one['ExternalIP'];
                    break;
                }
            }
            //get secondaryIps
            $filteredResultPackageSecondaryIPs = array_filter($resultPackageIPs['PackageIPAddress'], static function (array $e) {
                return !$e['IsPrimary']; //return only if false
            });
        }

        if (empty($mainIp)) {
            $this->renamePackageAndCancel($esPackages, $packageId, $possibleSpace->getDefaultHostingPlan()->getName());
            $this->saveAuditLogAndThrowDomainException($records, "Can not get the VM main ip.");
        }

        $this->saveAuditLog($records);

        return [
            'is_user_exists' => $userExists,
            'solidcp_package_id' => $packageId,
            'vps' => [
                'solidcp_item_id' => $vmResultArray['Value'],
                'provisioning_status' => 'InProgress', //this is default value when a server starts to create
                'main_ip' => $mainIp, //$vmItemResult['ExternalIP']
                'secondary_ips' => array_values( //rebuild array to start it from 0 index
                    array_map(static function (array $data) { //return simply array of SecondaryIps
                        return $data['ExternalIP'];
                    }, $filteredResultPackageSecondaryIPs)
                )
            ],
            'solidcp_server_node' => [
                'node_id' => $possibleSpace->getSolidcpServer()->getId(),  //SolidCP server aka node
                'solidcp_hosting_space_id' => $possibleSpace->getSolidCpIdHostingSpace(), //hdd in SolidCP
            ],
        ];
    }

    private function saveAuditLogAndThrowDomainException(array $records, string $error): void
    {
        $records[] = Record::create('SOLIDCP_ERROR', [$error]);
        $this->saveAuditLog($records);
        throw new \DomainException($error);
    }

    private function saveAuditLog(array $records): void
    {
        $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
        $auditLogCommand = new AuditLog\Add\SolidCP\Command(
            $this->enterpriseDispatcher,
            $entity,
            TaskName::createSolidcpAllInOneVps(),
            $records
        );
        $this->auditLogHandler->handle($auditLogCommand);
    }

    /**
     * @throws \Exception
     */
    private function guardResultAndRenameProblemPackage(array $result, EsPackages $esPackages, int $packageId, string $hostingPlanName, array $auditLogRecords): void
    {
        if (!$result['IsSuccess']) {
            $this->renamePackageAndCancel($esPackages, $packageId, $hostingPlanName);
            $this->saveAuditLogAndThrowDomainException($auditLogRecords, $result['ErrorCodes']['string']);
        }
    }

    /**
     * @throws \Exception
     */
    private function renamePackageAndCancel(EsPackages $esPackages, int $packageId, string $hostingPlanName): void
    {
        //rename package
        $esPackages->updatePackageName($packageId, $hostingPlanName . "Canceled-ip-or-vm-problem");
        //cancel package
        $esPackages->changePackageStatus($packageId, PackageStatus::cancelled());
    }

    private function recursive_array_search($needle, array $haystack)
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value || (is_array($value) && $this->recursive_array_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }
}