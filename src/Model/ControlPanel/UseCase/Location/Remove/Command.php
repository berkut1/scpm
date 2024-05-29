<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Remove;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}