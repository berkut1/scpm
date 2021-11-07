<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\Create;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\Generators;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;

class Handler
{
    private EnterpriseServerRepository $enterpriseServerRepository;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository, AuditLog\Add\SolidCP\Handler $auditLogHandler)
    {
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->auditLogHandler = $auditLogHandler;
    }

    public function handle(Command $command, array &$auditLogRecords = [], bool $saveAuditLog = true): int
    {
        $enterpriseServer = $this->enterpriseServerRepository->getDefaultOrById($command->id_enterprise);
        $esPackages = EsPackages::createFromEnterpriseServer($enterpriseServer);

        if (empty($command->spaceName)) {
            $command->spaceName = Generators::generateRandomString();
        }
        $result = $esPackages->addPackageWithResources($command->userId, $command->planId, $command->spaceName);

        $records = [
            Record::create('SOLIDCP_CREATED_FOR_USER_ID_PACKAGE_NAME_WITH_ID', [
                $command->userId,
                $command->spaceName,
                $result,
            ]),
        ];
        $auditLogRecords = array_merge($auditLogRecords, $records);

        if ($saveAuditLog) {
            $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
            $auditLogCommand = new AuditLog\Add\SolidCP\Command(
                $enterpriseServer,
                $entity,
                TaskName::createSolidcpPackage(),
                $records
            );
            $this->auditLogHandler->handle($auditLogCommand);
        }

        return $result;
    }
}