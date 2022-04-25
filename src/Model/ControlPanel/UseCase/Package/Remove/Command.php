<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\Remove;

use App\Model\ControlPanel\Entity\Package\Id;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public Id $id;

    public function __construct(Id $id)
    {
        $this->id = $id;
    }
}