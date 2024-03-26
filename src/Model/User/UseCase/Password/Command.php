<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Password;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public string $id;

    #[Assert\NotCompromisedPassword]
    #[Assert\Length(min: 8)]
    public string $password = '';

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}