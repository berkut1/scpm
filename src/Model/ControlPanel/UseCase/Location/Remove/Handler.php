<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private LocationRepository $repository;
    private AuditLog\Add\Handler $auditLogHandlerAndFlush;

    public function __construct(Flusher $flusher, LocationRepository $repository, AuditLog\Add\Handler $auditLogHandlerAndFlush)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->auditLogHandlerAndFlush = $auditLogHandlerAndFlush;
    }

    public function handle(Command $command): void
    {
        $location = $this->repository->get($command->id);
        if($location->hasServers()){
            throw new \DomainException("Location {$location->getName()} assigned to Nodes");
        }
        $this->repository->remove($location);
        //$this->flusher->flush($location); flush in audit log
        $records = [
            Record::create('REMOVED_LOCATION_WITH_NAME', [
                $location->getName(),
            ]),
        ];
        $entity = new Entity(EntityType::cpLocation(), (string)$location->getId());
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeCpLocation(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}