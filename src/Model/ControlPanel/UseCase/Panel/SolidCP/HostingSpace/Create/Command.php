<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?string $name = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_enterprise_dispatcher = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_server = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_hosting_space = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $max_active_number = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $max_reserved_memory_mb = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $space_quota_gb = 0;
}