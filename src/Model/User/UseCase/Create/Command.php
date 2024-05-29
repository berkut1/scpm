<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?string $login = '';

    #[Assert\NotCompromisedPassword]
    #[Assert\Length(min: 8)]
    public ?string $password = '';

    #[Assert\NotBlank]
    public ?string $role = '';
}
