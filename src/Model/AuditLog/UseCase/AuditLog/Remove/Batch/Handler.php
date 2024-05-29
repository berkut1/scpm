<?php
declare(strict_types=1);

namespace App\Model\AuditLog\UseCase\AuditLog\Remove\Batch;

use App\Model\AuditLog\Entity\AuditLogRepository;
use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\EntityType;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\Entity\TaskName;
use App\Model\AuditLog\UseCase\AuditLog;

final readonly class Handler
{
    public function __construct(
        private AuditLogRepository   $auditLogRepository,
        private AuditLog\Add\Handler $auditLogHandlerAndFlush
    ) {}

    public function handle(Command $command): void
    {
        $this->auditLogRepository->removeByDateRange($command->startDate, $command->endDate);

        $records = [
            Record::create('REMOVED_LOGS_BY_DATE_RANGE', [
                $command->startDate,
                $command->endDate,
            ]),
        ];
        $entity = new Entity(EntityType::auditLog(), Id::ZEROS);
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeLog(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}