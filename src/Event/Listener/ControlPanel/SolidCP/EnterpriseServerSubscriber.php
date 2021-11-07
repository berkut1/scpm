<?php 
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\Event\EnterpriseServerCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\Event\EnterpriseServerDisabled;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\Event\EnterpriseServerEdited;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\Event\EnterpriseServerEnabled;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnterpriseServerSubscriber implements EventSubscriberInterface
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    #[ArrayShape([
        EnterpriseServerCreated::class => "string",
        EnterpriseServerEdited::class => "string",
        EnterpriseServerDisabled::class => "string",
        EnterpriseServerEnabled::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            EnterpriseServerCreated::class => 'onEnterpriseServerCreated',
            EnterpriseServerEdited::class => 'onEnterpriseServerEdited',
            EnterpriseServerDisabled::class => 'onEnterpriseServerDisabled',
            EnterpriseServerEnabled::class => 'onEnterpriseServerEnabled',
        ];
    }

    public function onEnterpriseServerCreated(EnterpriseServerCreated $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseServer(), (string)$event->enterpriseServer->getId());
        $records = [
            Record::create('CREATED_ENTERPRISE_SERVER_WITH_NAME', [
                $event->enterpriseServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpSolidcpEnterpriseServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onEnterpriseServerEdited(EnterpriseServerEdited $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseServer(), (string)$event->enterpriseServer->getId());
        $records = [
            Record::create('EDITED_ENTERPRISE_SERVER', [
                $event->enterpriseServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpSolidcpEnterpriseServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onEnterpriseServerDisabled(EnterpriseServerDisabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseServer(), (string)$event->enterpriseServer->getId());
        $records = [
            Record::create('DISABLED_ENTERPRISE_SERVER_NAME', [
                $event->enterpriseServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::disableCpSolidcpEnterpriseServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onEnterpriseServerEnabled(EnterpriseServerEnabled $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpEnterpriseServer(), (string)$event->enterpriseServer->getId());
        $records = [
            Record::create('ENABLED_ENTERPRISE_SERVER_NAME', [
                $event->enterpriseServer->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::enableCpSolidcpEnterpriseServer(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}