<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $hasher;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {

        if ($this->users->hasByLogin($command->login)) {
            throw new \DomainException('User with this login already exists.');
        }

        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            $command->login,
            $this->hasher->hash($command->password),
            new Role($command->role),
        );

        $this->users->add($user);

        $this->flusher->flush($user);
    }
}
