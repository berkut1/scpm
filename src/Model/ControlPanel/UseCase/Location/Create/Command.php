<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $name = '';
}