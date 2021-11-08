<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseDispatcherRepository $repository;
    private AuditLog\Add\Handler $auditLogHandlerAndFlush;

    public function __construct(Flusher $flusher, EnterpriseDispatcherRepository $repository, AuditLog\Add\Handler $auditLogHandlerAndFlush)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->auditLogHandlerAndFlush = $auditLogHandlerAndFlush;
    }

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->repository->get($command->id);
        if($enterpriseDispatcher->hasServers()){
            throw new \DomainException("Enterprise Dispatcher {$enterpriseDispatcher->getName()} assigned to Nodes");
        }
        $this->repository->remove($enterpriseDispatcher);
        //$this->flusher->flush($enterpriseDispatcher); flush in audit log
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