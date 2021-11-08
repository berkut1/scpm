<?php 
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherDisabled;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherEdited;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherEnabled;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnterpriseDispatcherSubscriber implements EventSubscriberInterface
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    #[ArrayShape([
        EnterpriseDispatcherCreated::class => "string",
        EnterpriseDispatcherEdited::class => "string",
        EnterpriseDispatcherDisabled::class => "string",
        EnterpriseDispatcherEnabled::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            EnterpriseDispatcherCreated::class => 'onEnterpriseDispatcherCreated',
            EnterpriseDispatcherEdited::class => 'onEnterpriseDispatcherEdited',
            EnterpriseDispatcherDisabled::class => 'onEnterpriseDispatcherDisabled',
            EnterpriseDispatcherEnabled::class => 'onEnterpriseDispatcherEnabled',
        ];
    }

    public function onEnterpriseDispatcherCreated(EnterpriseDispatcherCreated $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseDispatcher(), (string)$event->enterpriseDispatcher->getId());
        $records = [
            Record::create('CREATED_ENTERPRISE_DISPATCHER_WITH_NAME', [
                $event->enterpriseDispatcher->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpSolidcpEnterpriseDispatcher(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onEnterpriseDispatcherEdited(EnterpriseDispatcherEdited $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseDispatcher(), (string)$event->enterpriseDispatcher->getId());
        $records = [
            Record::create('EDITED_ENTERPRISE_DISPATCHER', [
                $event->enterpriseDispatcher->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpSolidcpEnterpriseDispatcher(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onEnterpriseDispatcherDisabled(EnterpriseDispatcherDisabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseDispatcher(), (string)$event->enterpriseDispatcher->getId());
        $records = [
            Record::create('DISABLED_ENTERPRISE_DISPATCHER_NAME', [
                $event->enterpriseDispatcher->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::disableCpSolidcpEnterpriseDispatcher(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onEnterpriseDispatcherEnabled(EnterpriseDispatcherEnabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseDispatcher(), (string)$event->enterpriseDispatcher->getId());
        $records = [
            Record::create('ENABLED_ENTERPRISE_DISPATCHER_NAME', [
                $event->enterpriseDispatcher->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::enableCpSolidcpEnterpriseDispatcher(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}