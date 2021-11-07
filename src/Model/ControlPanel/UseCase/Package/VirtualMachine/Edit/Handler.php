<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\VirtualMachine\Edit;

use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackageRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private VirtualMachinePackageRepository $repository;

    public function __construct(Flusher $flusher, VirtualMachinePackageRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $virtualMachinePackage = $this->repository->get($command->id);

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