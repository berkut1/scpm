<?php
declare(strict_types=1);

namespace App\Event\Listener\ControlPanel\Package;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\Event\VirtualMachinePackageCreated;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\Event\VirtualMachinePackageEdited;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class VirtualMachinePackageSubscriber implements EventSubscriberInterface
{
    public function __construct(private AuditLog\Add\Handler $auditLogHandler) {}

    #[ArrayShape([
        VirtualMachinePackageCreated::class => "string",
        VirtualMachinePackageEdited::class => "string",
    ])]
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            VirtualMachinePackageCreated::class => 'onVirtualMachinePackageCreated',
            VirtualMachinePackageEdited::class => 'onVirtualMachinePackageEdited',
        ];
    }

    public function onVirtualMachinePackageCreated(VirtualMachinePackageCreated $event): void
    {
        $entity = new Entity(EntityType::cpPackageVirtualMachine(), $event->virtualMachinePackage->getId()->getValue());
        $records = [
            Record::create('CREATED_VIRTUAL_MACHINE_PACKAGE_WITH_NAME', [
                $event->virtualMachinePackage->getPackage()->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createCpPackageVirtualMachine(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }

    public function onVirtualMachinePackageEdited(VirtualMachinePackageEdited $event): void
    {
        $entity = new Entity(EntityType::cpPackageVirtualMachine(), $event->virtualMachinePackage->getId()->getValue());
        $records = [
            Record::create('EDITED_VIRTUAL_MACHINE_PACKAGE_WITH_NAME', [
                $event->virtualMachinePackage->getPackage()->getName(),
            ]),
        ];
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::editCpPackageVirtualMachine(), $records);
        $this->auditLogHandler->handle($auditLogCommand);
    }
}