<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeState;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\VirtualizationServer2012\VirtualMachineRequestedState;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;
use App\Model\ControlPanel\Service\SolidCP\ServerService;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package;

final readonly class Handler
{
    public function __construct(
        private EnterpriseDispatcherRepository $enterpriseDispatcherRepository,
        private ServerService                  $serverService,
        private AuditLog\Add\SolidCP\Handler   $auditLogHandler
    ) {}

    public function handle(Command $command, bool $saveAuditLog = true): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        if (!$enterpriseDispatcher->isEnabled()) {
            throw new \DomainException("The EnterpriseDispatcher {$enterpriseDispatcher->getName()} is disabled");
        }

        $ip = $this->serverService->ipAddressVpsExternalNetworkDetails($enterpriseDispatcher->getId(), $command->vps_ip_address);
        if ($ip['UserName'] === $enterpriseDispatcher->getLogin()) {
            throw new \LogicException("You can not change status of yourself package");
        }
        if ($ip['UserName'] !== $command->client_login) {
            throw new \DomainException("This IP is currently owned by client {$ip['UserName']}, not client {$command->client_login}");
        }
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $result = $esVirtualizationServer2012->changeVirtualMachineState($ip['ItemId'], new VirtualMachineRequestedState($command->vps_state));
        $records = [
            Record::create('SOLIDCP_CHANGED_USER_VPS_WITH_IP_TO_STATE_WITH_SUCCESS_RESULT', [
                $command->client_login,
                $command->vps_ip_address,
                $command->vps_state,
                isset($result['IsSuccess']) ? var_export($result['IsSuccess'], true) : 'n/a',
            ]),
        ];

        if ($saveAuditLog) {
            $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
            $auditLogCommand = new AuditLog\Add\SolidCP\Command(
                $enterpriseDispatcher,
                $entity,
                TaskName::changeSolidcpUserVpsState(),
                $records
            );
            $this->auditLogHandler->handle($auditLogCommand);
        }

        if (isset($result['IsSuccess']) && $result['IsSuccess'] === true) { //SolidCP put there useless information if IsSuccess is true
            $result = [
                "IsSuccess" => $result['IsSuccess'], //left only result if success
            ];
        }
        return $result;
    }
}
