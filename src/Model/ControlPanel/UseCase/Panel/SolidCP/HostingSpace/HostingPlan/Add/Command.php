<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Add;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    private readonly ?int $id_hosting_space;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $solidcp_id_plan = 0;

    #[Assert\NotBlank]
    public ?string $name = '';

    public function __construct(int $id_hosting_space)
    {
        $this->id_hosting_space = $id_hosting_space;
    }

    public function getIdHostingSpace(): int
    {
        return $this->id_hosting_space;
    }
}