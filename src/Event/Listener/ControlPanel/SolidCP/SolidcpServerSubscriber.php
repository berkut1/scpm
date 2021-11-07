<?php 
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerDisabled;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerEdited;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerEnabled;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SolidcpServerSubscriber implements EventSubscriberInterface
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    #[ArrayShape([
        SolidcpServerCreated::class => "string",
        SolidcpServerEdited::class => "string",
        SolidcpServerDisabled::class => "string",
        SolidcpServerEnabled::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            SolidcpServerCreated::class => 'onSolidcpServerCreated',
            SolidcpServerEdited::class => 'onSolidcpServerEdited',
            SolidcpServerDisabled::class => 'onSolidcpServerDisabled',
            SolidcpServerEnabled::class => 'onSolidcpServerEnabled',
        ];
    }

    public function onSolidcpServerCreated(SolidcpServerCreated $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpServer(), (string)$event->solidcpServer->getId());
        $records = [
            Record::create('CREATED_SOLIDCP_SERVER_WITH_NAME', [
                $event->solidcpServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpSolidcpServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpServerEdited(SolidcpServerEdited $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpServer(), (string)$event->solidcpServer->getId());
        $records = [
            Record::create('EDITED_SOLIDCP_SERVER_WITH_NAME', [
                $event->solidcpServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpSolidcpServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpServerDisabled(SolidcpServerDisabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpServer(), (string)$event->solidcpServer->getId());
        $records = [
            Record::create('DISABLED_SOLIDCP_SERVER_NAME', [
                $event->solidcpServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::disableCpSolidcpServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpServerEnabled(SolidcpServerEnabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpServer(), (string)$event->solidcpServer->getId());
        $records = [
            Record::create('ENABLED_SOLIDCP_SERVER_NAME', [
                $event->solidcpServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::enableCpSolidcpServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}