<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Suspend;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

final readonly class Handler
{
    public function __construct(private UserRepository $users, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $user->suspend();

        $this->flusher->flush();
    }
}
