<?php
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\Package;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Package\Event\PackageChangedSolidCpPlans;
use App\Model\ControlPanel\Entity\Package\Event\PackageRenamed;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PackageSubscriber implements EventSubscriberInterface
{
    private AuditLog\Add\Handler $auditLogHandler;

    public function __construct(AuditLog\Add\Handler $auditLogHandler)
    {
        $this->auditLogHandler = $auditLogHandler;
    }

    #[ArrayShape([
        PackageRenamed::class => "string",
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            PackageRenamed::class => 'onPackageRenamed',
            PackageChangedSolidCpPlans::class => 'onPackageChangedSolidCpPlans',
        ];
    }

    public function onPackageRenamed(PackageRenamed $event): void
    {
        $entity = new Entity(EntityType::cpPackage(), $event->package->getId()->getValue());
        $records = [
            Record::create('RENAMED_PACKAGE_WITH_NAME_TO_NEW_NAME', [
                $event->oldName,
                $event->package->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::renameCpPackage(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onPackageChangedSolidCpPlans(PackageChangedSolidCpPlans $event): void
    {
        $entity = new Entity(EntityType::cpPackage(), $event->package->getId()->getValue());
        $records = [
            Record::create('CHANGED_SOLIDCP_PLANS_IN_PACKAGE_NAME', [
                $event->package->getName(),
            ]),
        ];
        foreach ($event->removedPlans as $removedPlan){
            $records[] = Record::create('REMOVED_SOLIDCP_PLAN_NAME_FROM_PACKAGE', [
                $removedPlan->getName(),
            ]);
        }
        foreach ($event->addedPlans as $addedPlan){
            $records[] = Record::create('ADDED_SOLIDCP_PLAN_NAME_TO_PACKAGE', [
                $addedPlan->getName(),
            ]);
        }

        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::changeSolidCpPlansCpPackage(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}