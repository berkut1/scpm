<?php
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceAddedOsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceChangedNode;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceChangedSolidCpHostingSpaceId;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceDisabled;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceEdited;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceEnabled;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceRemovedOsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceRemovedPlan;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SolidcpHostingSpaceSubscriber implements EventSubscriberInterface
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    #[ArrayShape([
        SolidcpHostingSpaceCreated::class => "string",
        SolidcpHostingSpaceEdited::class => "string",
        SolidcpHostingSpaceChangedNode::class => "string",
        SolidcpHostingSpaceChangedSolidCpHostingSpaceId::class => "string",
        SolidcpHostingSpaceRemovedPlan::class => "string",
        SolidcpHostingSpaceAddedOsTemplate::class => "string",
        SolidcpHostingSpaceRemovedOsTemplate::class => "string",
        SolidcpHostingSpaceDisabled::class => "string",
        SolidcpHostingSpaceEnabled::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            SolidcpHostingSpaceCreated::class => 'onSolidcpHostingSpaceCreated',
            SolidcpHostingSpaceEdited::class => 'onSolidcpHostingSpaceEdited',
            SolidcpHostingSpaceChangedNode::class => 'onSolidcpHostingSpaceChangedNode',
            SolidcpHostingSpaceChangedSolidCpHostingSpaceId::class => 'onSolidcpHostingSpaceChangedSolidCpHostingSpaceId',
            SolidcpHostingSpaceRemovedPlan::class => 'onSolidcpHostingSpaceRemovedPlan',
            SolidcpHostingSpaceAddedOsTemplate::class => 'onSolidcpHostingSpaceAddedOsTemplate',
            SolidcpHostingSpaceRemovedOsTemplate::class => 'onSolidcpHostingSpaceRemovedOsTemplate',
            SolidcpHostingSpaceDisabled::class => 'onSolidcpHostingSpaceDisabled',
            SolidcpHostingSpaceEnabled::class => 'onSolidcpHostingSpaceEnabled',
        ];
    }

    public function onSolidcpHostingSpaceCreated(SolidcpHostingSpaceCreated $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('CREATED_SOLIDCP_HOSTING_SPACE_WITH_NAME', [
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceEdited(SolidcpHostingSpaceEdited $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('EDITED_SOLIDCP_HOSTING_SPACE_WITH_NAME', [
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceChangedNode(SolidcpHostingSpaceChangedNode $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('CHANGED_SOLIDCP_HOSTING_SPACE_WITH_NAME_NODE_FROM_NAME_TO_NAME', [
                $event->solidcpHostingSpace->getName(),
                $event->oldNodeName,
                $event->newNodeName,
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceChangedSolidCpHostingSpaceId(SolidcpHostingSpaceChangedSolidCpHostingSpaceId $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('CHANGED_SOLIDCP_HOSTING_SPACE_WITH_NAME_SOLIDCPID_FROM_ID_TO_ID', [
                $event->solidcpHostingSpace->getName(),
                $event->oldSolidCpHostingSpaceId,
                $event->newSolidCpHostingSpaceId,
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceRemovedPlan(SolidcpHostingSpaceRemovedPlan $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('REMOVED_PLAN_NAME_FROM_HOSTING_SPACE_WITH_NAME', [
                $event->planName,
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::removeCpSolidcpHostingPlan(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceAddedOsTemplate(SolidcpHostingSpaceAddedOsTemplate $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('ADDED_OS_TEMPLATE_NAME_WITH_PATH_TO_HOSTING_SPACE_NAME', [
                $event->osTemplate->getName(),
                $event->osTemplate->getPath(),
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::addOsTemplateCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceRemovedOsTemplate(SolidcpHostingSpaceRemovedOsTemplate $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('REMOVED_OS_TEMPLATE_NAME_WITH_PATH_FROM_HOSTING_SPACE_NAME', [
                $event->osTemplate->getName(),
                $event->osTemplate->getPath(),
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::removeOsTemplateCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceDisabled(SolidcpHostingSpaceDisabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('DISABLED_HOSTING_SPACE_NAME', [
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::disableCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingSpaceEnabled(SolidcpHostingSpaceEnabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingSpace(), (string)$event->solidcpHostingSpace->getId());
        $records = [
            Record::create('ENABLED_HOSTING_SPACE_NAME', [
                $event->solidcpHostingSpace->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::enableCpSolidcpHostingSpace(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}