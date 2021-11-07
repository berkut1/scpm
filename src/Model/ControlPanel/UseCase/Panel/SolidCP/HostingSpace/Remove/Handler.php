<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $repository;
    private AuditLog\Add\Handler $auditLogHandlerAndFlush;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $repository, AuditLog\Add\Handler $auditLogHandlerAndFlush)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->auditLogHandlerAndFlush = $auditLogHandlerAndFlush;
    }

    public function handle(Command $command): void
    {
        $solidcpServer = $this->repository->get($command->id);
        if($solidcpServer->hasPlans()){
            throw new \DomainException("Solidcp Hosting Space {$solidcpServer->getName()} has Plans");
        }
        $this->repository->remove($solidcpServer);
        //$this->flusher->flush($solidcpServer); flush in audit log
        $records = [
            Record::create('REMOVED_SOLIDCP_HOSTING_SPACE_WITH_NAME', [
                $solidcpServer->getName(),
            ]),
        ];
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$solidcpServer->getId());
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeCpSolidcpHostingSpace(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}