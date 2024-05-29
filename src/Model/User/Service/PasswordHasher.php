<?php
declare(strict_types=1);

namespace App\Model\User\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class PasswordHasher
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function hash(PasswordAuthenticatedUserInterface $user, string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }

    public function isPasswordValid(PasswordAuthenticatedUserInterface $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    public function needsRehash(PasswordAuthenticatedUserInterface $user): bool
    {
        return $this->passwordHasher->needsRehash($user);
    }
}