<?php
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Location\Event\LocationCreated;
use App\Model\ControlPanel\Entity\Location\Event\LocationRenamed;
use App\Model\ControlPanel\Entity\Location\LocationRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class LocationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AuditLog\Add\Handler $auditLogHandler,
        private LocationRepository   $locationRepository
    ) {}

    #[ArrayShape([LocationCreated::class => "string", LocationRenamed::class => "string"])]
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            LocationCreated::class => 'onLocationCreated',
            LocationRenamed::class => 'onLocationRenamed',
        ];
    }

    public function onLocationCreated(LocationCreated $event): void
    {
        $location = $this->locationRepository->getByName($event->name);
        $entity = new Entity(EntityType::cpLocation(), (string)$location->getId());
        $records = [
            Record::create('CREATED_LOCATION_WITH_NAME', [
                $location->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpLocation(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onLocationRenamed(LocationRenamed $event): void
    {
        $location = $event->location;
        $entity = new Entity(EntityType::cpLocation(), (string)$location->getId());
        $records = [
            Record::create('RENAMED_LOCATION_WITH_NAME_TO_NAME', [
                $event->oldName,
                $location->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::renameCpLocation(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}