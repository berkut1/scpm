<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseServerRepository $repository;
    private AuditLog\Add\Handler $auditLogHandlerAndFlush;

    public function __construct(Flusher $flusher, EnterpriseServerRepository $repository, AuditLog\Add\Handler $auditLogHandlerAndFlush)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->auditLogHandlerAndFlush = $auditLogHandlerAndFlush;
    }

    public function handle(Command $command): void
    {
        $enterpriseServer = $this->repository->get($command->id);
        if($enterpriseServer->hasServers()){
            throw new \DomainException("Enterprise Server {$enterpriseServer->getName()} assigned to Nodes");
        }
        $this->repository->remove($enterpriseServer);
        //$this->flusher->flush($enterpriseServer); flush in audit log
        $records = [
            Record::create('REMOVED_ENTERPRISE_SERVER_WITH_NAME', [
                $enterpriseServer->getName(),
            ]),
        ];
        $entity = new Entity(EntityType::cpSolidcpEnterpriseServer(), (string)$enterpriseServer->getId());
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeCpSolidcpEnterpriseServer(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}