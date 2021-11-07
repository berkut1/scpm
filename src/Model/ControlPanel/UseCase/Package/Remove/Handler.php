<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\Remove;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\ControlPanel\Entity\Package\PackageRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private AuditLog\Add\Handler $auditLogHandlerAndFlush;
    private PackageRepository $repository;

    public function __construct(Flusher $flusher, AuditLog\Add\Handler $auditLogHandlerAndFlush, PackageRepository $repository)
    {
        $this->flusher = $flusher;
        $this->auditLogHandlerAndFlush = $auditLogHandlerAndFlush;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $package = $this->repository->getPackage($command->id);
        if($package->hasAssignedItems()){
            throw new \DomainException("Package {$package->getName()} assigned to Plans/Items. Deassign them first.");
        }
        $this->repository->remove($package);
        //$this->flusher->flush($package); flush in audit log
        $records = [
            Record::create('REMOVED_PACKAGE_WITH_NAME', [
                $package->getName(),
            ]),
        ];
        $entity = new Entity(EntityType::cpPackage(), $package->getId()->getValue());
        $auditLogCommand = new AuditLog\Add\Command(
            $entity,
            TaskName::removeCpPackage(),
            $records
        );
        $this->auditLogHandlerAndFlush->handle($auditLogCommand);
    }
}