<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Location\LocationRepository;

final readonly class Handler
{
    public function __construct(private LocationRepository $repository, private AuditLog\Add\Handler $auditLogHandlerAndFlush) {}

    public function handle(Command $command): void
    {
        $location = $this->repository->get($command->id);
        if ($location->hasServers()) {
            throw new \DomainException("Location {$location->getName()} assigned to Nodes");
        }
        $this->repository->remove($location);

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