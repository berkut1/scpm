<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;

final readonly class Handler
{
    public function __construct(
        private EnterpriseDispatcherRepository $repository,
        private AuditLog\Add\Handler           $auditLogHandlerAndFlush
    ) {}

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->repository->get($command->id);
        if ($enterpriseDispatcher->hasServers()) {
            throw new \DomainException("Enterprise Dispatcher {$enterpriseDispatcher->getName()} assigned to Nodes");
        }
        $this->repository->remove($enterpriseDispatcher);

        $records = [
            Record::create('REMOVED_ENTERPRISE_SERVER_WITH_NAME', [
                $enterpriseDispatcher->getName(),
            ]),
        ];
        $entity = new Entity(EntityType::cpSolidcpEnterpriseDispatcher(), (string)$enterpriseDispatcher->getId());
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeCpSolidcpEnterpriseDispatcher(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}