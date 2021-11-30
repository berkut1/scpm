<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Password;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Security\UserIdentity;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $hasher;
    private Flusher $flusher;

    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $userIdentity = new UserIdentity(
            Id::next()->getValue(),
            $user->getLogin(),
            '',
            $user->getRole()->getName(),
            $user->getStatus()->getName()
        );

        $hash = $this->hasher->hash($userIdentity, $command->password);
        $user->changePassword($hash);


        $this->flusher->flush();
    }
}