<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User\UserInfo;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User\UserRole;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User\UserStatus;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;
use App\Model\ControlPanel\UseCase\AuditLog;

final readonly class Handler
{
    public function __construct(
        private EnterpriseDispatcherRepository $enterpriseDispatcherRepository,
        private AuditLog\Add\SolidCP\Handler   $auditLogHandler
    ) {}

    public function handle(Command $command, array &$auditLogRecords = [], bool $saveAuditLog = true): int
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $esUsers = EsUsers::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $user = new UserInfo(
            $enterpriseDispatcher->getSolidcpLoginId(),
            UserRole::user(),
            UserStatus::active(),
            false,
            false,
            $command->username,
            $command->firstName ?? $command->username,
            $command->lastName ?? $command->username,
            $command->email,
            true
        );

        $result = $esUsers->addUser($user, $command->password);
        $records = [
            Record::create('SOLIDCP_CREATED_USER_WITH_ID', [
                $command->username,
                $result,
            ]),
        ];
        $auditLogRecords = array_merge($auditLogRecords, $records);

        if ($saveAuditLog) {
            $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
            $auditLogCommand = new AuditLog\Add\SolidCP\Command(
                $enterpriseDispatcher,
                $entity,
                TaskName::createSolidcpUser(),
                $records
            );
            $this->auditLogHandler->handle($auditLogCommand);
        }

        return $result;
    }
}