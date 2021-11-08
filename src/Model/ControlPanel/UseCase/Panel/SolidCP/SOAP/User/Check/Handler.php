<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository, AuditLog\Add\SolidCP\Handler $auditLogHandler)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->auditLogHandler = $auditLogHandler;
    }

    /**
     * @throws \Exception
     */
    public function handle(Command $command): bool
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $esUsers = EsUsers::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
        $auditLogCommand = new AuditLog\Add\SolidCP\Command(
            $enterpriseDispatcher,
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