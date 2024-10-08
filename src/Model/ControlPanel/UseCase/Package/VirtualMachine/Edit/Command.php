<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\VirtualMachine\Edit;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?Id $id;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $cores = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $threads = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $ram_mb = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $space_gb = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public ?int $iops_min = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public ?int $iops_max = null;

    private function __construct(Id $id)
    {
        $this->id = $id;
    }

    public static function fromVirtualMachine(VirtualMachinePackage $virtualMachinePackage): self
    {
        $command = new self($virtualMachinePackage->getId());
        $command->cores = $virtualMachinePackage->getCores();
        $command->threads = $virtualMachinePackage->getThreads();
        $command->ram_mb = $virtualMachinePackage->getRamMb();
        $command->space_gb = $virtualMachinePackage->getSpaceGb();
        $command->iops_min = $virtualMachinePackage->getIopsMin();
        $command->iops_max = $virtualMachinePackage->getIopsMax();
        return $command;
    }
}