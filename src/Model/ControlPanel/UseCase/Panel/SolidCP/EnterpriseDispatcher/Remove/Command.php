<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $id = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}