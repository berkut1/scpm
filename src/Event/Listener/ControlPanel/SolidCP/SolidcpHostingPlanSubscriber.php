<?php
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\Event\SolidcpHostingPlanCreated;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class SolidcpHostingPlanSubscriber implements EventSubscriberInterface
{
    public function __construct(private AuditLog\Add\Handler $auditLogHandler) {}

    #[ArrayShape([
        SolidcpHostingPlanCreated::class => "string",
    ])]
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            SolidcpHostingPlanCreated::class => 'onSolidcpHostingPlanCreated',
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
}