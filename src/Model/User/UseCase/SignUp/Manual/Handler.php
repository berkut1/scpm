<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Manual;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Flusher;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;
    private PasswordHasher $hashing;

    public function __construct(UserRepository $users, PasswordHasher $hashing, Flusher $flusher)
    {
        $this->users = $users;
        $this->hashing = $hashing;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            $command->login,
            $this->hashing->hash($command->password),
            new Role($command->role)
        );

        $this->users->add($user);

        $this->flusher->flush($user);
    }
}
