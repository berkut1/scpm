<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;

class Handler
{
    private EnterpriseServerRepository $enterpriseServerRepository;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository, AuditLog\Add\SolidCP\Handler $auditLogHandler)
    {
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->auditLogHandler = $auditLogHandler;
    }

    /**
     * @throws \Exception
     */
    public function handle(Command $command): bool
    {
        $enterpriseServer = $this->enterpriseServerRepository->getDefaultOrById($command->id_enterprise);

        $esUsers = EsUsers::createFromEnterpriseServer($enterpriseServer);
        $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
        $auditLogCommand = new AuditLog\Add\SolidCP\Command(
            $enterpriseServer,
            $entity,
            TaskName::checkSolidcpUser(),
            [
                Record::create('SOLIDCP_CHECKED_USER', [
                    $command->username,
                ]),
            ]
        );
        $this->auditLogHandler->handle($auditLogCommand);
        return $esUsers->userExists($command->username);
    }
}