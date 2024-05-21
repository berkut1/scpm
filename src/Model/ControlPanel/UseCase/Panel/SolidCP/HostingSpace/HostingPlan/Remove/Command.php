<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Remove;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_plan = 0;

    public function __construct(int $id, int $id_plan)
    {
        $this->id = $id;
        $this->id_plan = $id_plan;
    }
}
