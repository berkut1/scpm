<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpServerRepository $repository;
    private AuditLog\Add\Handler $auditLogHandlerAndFlush;

    public function __construct(Flusher $flusher, SolidcpServerRepository $repository, AuditLog\Add\Handler $auditLogHandlerAndFlush)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->auditLogHandlerAndFlush = $auditLogHandlerAndFlush;
    }

    public function handle(Command $command): void
    {
        $solidcpServer = $this->repository->get($command->id);
        if($solidcpServer->hasHostingSpace()){
            throw new \DomainException("Solidcp Server/Node {$solidcpServer->getName()} has Hosting Spaces");
        }
        $this->repository->remove($solidcpServer);
        //$this->flusher->flush($solidcpServer); flush in audit log
        $records = [
            Record::create('REMOVE_SOLIDCP_SERVER_WITH_NAME', [
                $solidcpServer->getName(),
            ]),
        ];
        $entity = new Entity(EntityType::cpSolidcpServer(), (string)$solidcpServer->getId());
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeCpSolidcpServer(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}