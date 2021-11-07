<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\VirtualMachine\Create;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackageRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private VirtualMachinePackageRepository $virtualMachinePackageRepository;

    public function __construct(Flusher $flusher, VirtualMachinePackageRepository $virtualMachinePackageRepository)
    {
        $this->flusher = $flusher;
        $this->virtualMachinePackageRepository = $virtualMachinePackageRepository;
    }

    public function handle(Command $command): void
    {
        if ($this->virtualMachinePackageRepository->hasByName($command->name)) {
            throw new \DomainException('VirtualMachinePackage with this name already exists.');
        }

        $virtualMachinePackage = new VirtualMachinePackage(
            Id::next(),
            $command->name,
            $command->cores,
            $command->threads,
            $command->ram_mb,
            $command->space_gb,
            $command->iops_min,
            $command->iops_max
        );
        $this->virtualMachinePackageRepository->add($virtualMachinePackage);
        $this->flusher->flush($virtualMachinePackage);
    }
}