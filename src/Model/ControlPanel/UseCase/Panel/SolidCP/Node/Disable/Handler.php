<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Disable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                 $flusher,
        private SolidcpServerRepository $repository
    ) {}

    public function handle(Command $command): void
    {
        $solidcpServer = $this->repository->get($command->id_solidcp_server);
        $solidcpServer->disable();
        $this->flusher->flush($solidcpServer);
    }
}