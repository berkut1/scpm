<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\ChangeStatus;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\Package\PackageStatus;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;
use App\Model\ControlPanel\UseCase\AuditLog;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository, AuditLog\Add\SolidCP\Handler $auditLogHandler)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->auditLogHandler = $auditLogHandler;
    }

    public function handle(Command $command, array &$auditLogRecords = [], bool $saveAuditLog = true): void
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $esPackages = EsPackages::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $esPackages->changePackageStatus($command->solidcp_package_id, new PackageStatus($command->solidcp_package_status));

        $auditLogRecords[] = Record::create('SOLIDCP_CHANGED_PACKAGE_WITH_ID_TO_STATUS', [
            $command->solidcp_package_id,
            $command->solidcp_package_status,
        ]);

        if ($saveAuditLog) {
            $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
            $auditLogCommand = new AuditLog\Add\SolidCP\Command(
                $enterpriseDispatcher,
                $entity,
                TaskName::changeSolidcpPackageStatus(),
                $auditLogRecords
            );
            $this->auditLogHandler->handle($auditLogCommand);
        }
    }
}
