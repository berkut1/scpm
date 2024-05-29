<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Disable;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_hosting_space = 0;

    public function __construct(int $id_hosting_space)
    {
        $this->id_hosting_space = $id_hosting_space;
    }
}