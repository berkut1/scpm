<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\CreateVM;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackageRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine\VirtualMachine;
use App\Model\ControlPanel\Service\Generators;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;

class Handler
{
    private EnterpriseServerRepository $enterpriseServerRepository;
    private VirtualMachinePackageRepository $virtualMachinePackageRepository;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository,
                                VirtualMachinePackageRepository $virtualMachinePackageRepository,
                                AuditLog\Add\SolidCP\Handler $auditLogHandler)
    {
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->virtualMachinePackageRepository = $virtualMachinePackageRepository;
        $this->auditLogHandler = $auditLogHandler;
    }

    public function handle(Command $command, array &$auditLogRecords = [], bool $saveAuditLog = true): array
    {
        $enterpriseServer = $this->enterpriseServerRepository->getDefaultOrById($command->id_enterprise);
        $virtualMachinePackage = $this->virtualMachinePackageRepository->get(new Id($command->id_package_virtual_machines));
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseServer($enterpriseServer);

        $vmSettings = VirtualMachine::create(
            $command->packageId,
            $virtualMachinePackage->getCores(),
            $virtualMachinePackage->getRamMb(),
            [$virtualMachinePackage->getSpaceGb()],
            $virtualMachinePackage->getIopsMin(),
            $virtualMachinePackage->getIopsMax(),
            $command->snapshotsNumber,
            $command->hostname,
            $command->dvdDriveInstalled,
            $command->bootFromCD,
            $command->numLockEnabled,
            $command->startTurnOffAllowed,
            $command->pauseResumeAllowed,
            $command->rebootAllowed,
            $command->resetAllowed,
            $command->reinstallAllowed,
            $command->externalNetworkEnabled,
            null,//Generators::generateMsVirtualMacAddress(),
            $command->privateNetworkEnabled,
            $command->defaultaccessvlan
        );
        //dump($vmSettings);

        if(!$command->externalNetworkEnabled){
            $command->externalAddressesNumber = 0;
            $command->randomExternalAddresses = false;
        }
        if(!$command->privateNetworkEnabled){
            $command->privateAddressesNumber = 0;
            $command->randomPrivateAddresses = false;
        }

        $result = $esVirtualizationServer2012->createNewVirtualMachine(
            $vmSettings,
            $command->osTemplateFile,
            $command->password,
            $command->externalAddressesNumber,
            $command->randomExternalAddresses,
            [],
            $command->privateAddressesNumber,
            $command->randomPrivateAddresses,
        );
        //throw new \DomainException('CreateNewVirtualMachine: ' . $result);//

        $records = [
            Record::create('SOLIDCP_CREATED_VM_WITH_ITEM_ID_ON_PACKAGE_ID', [
                $result['Value'],
                $command->packageId,
            ]),
        ];
        $auditLogRecords = array_merge($auditLogRecords, $records);

        if ($saveAuditLog) {
            $entity = new Entity(EntityType::soapExecute(), \App\Model\AuditLog\Entity\Id::zeros()->getValue());
            $auditLogCommand = new AuditLog\Add\SolidCP\Command(
                $enterpriseServer,
                $entity,
                TaskName::createSolidcpVps(),
                $records
            );
            $this->auditLogHandler->handle($auditLogCommand);
        }


        return $result;
        //  "IsSuccess" => true
        //  "ErrorCodes" => []
        //  "Value" => 753
    }
}