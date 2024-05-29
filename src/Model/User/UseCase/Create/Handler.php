<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\Status;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Security\UserIdentity;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private Flusher        $flusher
    ) {}

    public function handle(Command $command): void
    {

        if ($this->users->hasByLogin($command->login)) {
            throw new \DomainException('User with this login already exists.');
        }

        $userIdentity = new UserIdentity(
            Id::next()->getValue(),
            $command->login,
            '',
            $command->role,
            Status::active()->getName()
        );
        $hash = $this->hasher->hash($userIdentity, $command->password);

        $user = User::create(
            new Id($userIdentity->getId()),
            new \DateTimeImmutable(),
            $command->login,
            $hash,
            new Role($command->role),
        );

        $this->users->add($user);

        $this->flusher->flush($user);
    }
}
