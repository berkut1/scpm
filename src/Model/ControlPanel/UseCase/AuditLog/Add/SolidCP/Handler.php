<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\AuditLog\Add\SolidCP;

use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;

class Handler
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    public function handle(Command $command): void
    {
        $records = [
            Record::create('SOLIDCP_USED_ENTERPRISE_URL_VIA_LOGIN_LOGIN_ID', [
                $command->enterpriseDispatcher->getUrl(),
                $command->enterpriseDispatcher->getLogin(),
                $command->enterpriseDispatcher->getSolidcpLoginId(),
            ]),
        ];
        $records = array_merge($records, $command->records);
        $auditLogCommand = new AuditLog\Add\Command($command->entity, $command->taskName, $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}