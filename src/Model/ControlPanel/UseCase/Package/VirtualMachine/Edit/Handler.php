<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\VirtualMachine\Edit;

use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackageRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                         $flusher,
        private VirtualMachinePackageRepository $repository
    ) {}

    public function handle(Command $command): void
    {
        $virtualMachinePackage = $this->repository->getVmPackage($command->id);

        $virtualMachinePackage->edit(
            $command->cores,
            $command->threads,
            $command->ram_mb,
            $command->space_gb,
            $command->iops_min,
            $command->iops_max);
        $this->flusher->flush($virtualMachinePackage);
    }
}