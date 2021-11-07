<?php 
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\Event\SolidcpHostingPlanCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\Event\SolidcpHostingPlanSetDefault;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\Event\SolidcpHostingPlanSetNonDefault;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SolidcpHostingPlanSubscriber implements EventSubscriberInterface
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    #[ArrayShape([SolidcpHostingPlanCreated::class => "string", SolidcpHostingPlanSetDefault::class => "string", SolidcpHostingPlanSetNonDefault::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            SolidcpHostingPlanCreated::class => 'onSolidcpHostingPlanCreated',
            SolidcpHostingPlanSetDefault::class => 'onSolidcpHostingPlanSetDefault',
            SolidcpHostingPlanSetNonDefault::class => 'onSolidcpHostingPlanSetNonDefault',
        ];
    }

    public function onSolidcpHostingPlanCreated(SolidcpHostingPlanCreated $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingPlan(), (string)$event->solidcpHostingPlan->getId());
        $records = [
            Record::create('CREATED_SOLIDCP_HOSTING_PLAN_WITH_NAME', [
                $event->solidcpHostingPlan->getName(),
            ]),
            Record::create('ASSIGNED_SOLIDCP_HOSTING_PLAN_WITH_NAME_TO_HOSTING_SPACE_NAME', [
                $event->solidcpHostingPlan->getName(),
                $event->solidcpHostingPlan->getHostingSpace()->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpSolidcpHostingPlan(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingPlanSetDefault(SolidcpHostingPlanSetDefault $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingPlan(), (string)$event->solidcpHostingPlan->getId());
        $records = [
            Record::create('SET_DEFAULT_SOLIDCP_HOSTING_PLAN_NAME_FOR_HOSTING_SPACE_NAME', [
                $event->solidcpHostingPlan->getName(),
                $event->solidcpHostingPlan->getHostingSpace()->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::setDefaultCpSolidcpHostingPlan(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onSolidcpHostingPlanSetNonDefault(SolidcpHostingPlanSetNonDefault $event): void
    {
        $entity = new Entity(EntityType::cpSolidcpHostingPlan(), (string)$event->solidcpHostingPlan->getId());
        $records = [
            Record::create('SET_NON_DEFAULT_SOLIDCP_HOSTING_PLAN_WITH_NAME', [
                $event->solidcpHostingPlan->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::setNonDefaultCpSolidcpHostingPlan(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}