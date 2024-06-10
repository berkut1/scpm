<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Manual;

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
        private PasswordHasher $hashing,
        private Flusher        $flusher
    ) {}

    public function handle(Command $command): void
    {
        $userIdentity = new UserIdentity(
            Id::next()->getValue(),
            $command->login,
            '',
            $command->role,
            Status::active()->getName()
        );
        $hash = $this->hashing->hash($userIdentity, $command->password);

        $user = User::create(
            new Id($userIdentity->getId()),
            new \DateTimeImmutable(),
            $command->login,
            $hash,
        );

        $this->users->add($user);

        $this->flusher->flush($user);
    }
}
