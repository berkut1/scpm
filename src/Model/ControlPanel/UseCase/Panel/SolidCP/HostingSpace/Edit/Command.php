<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public int $id;

    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $max_active_number = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $max_reserved_memory_mb = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $space_quota_gb = 0;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromHostingSpace(SolidcpHostingSpace $hostingSpace): self
    {
        $command = new self($hostingSpace->getId());
        $command->name = $hostingSpace->getName();
        $command->max_active_number = $hostingSpace->getMaxActiveNumber();
        $command->max_reserved_memory_mb = $hostingSpace->getMaxReservedMemoryKb() / 1024;
        $command->space_quota_gb = $hostingSpace->getSpaceQuotaGb();
        return $command;
    }
}