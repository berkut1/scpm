<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeNode;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                       $flusher,
        private SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository,
        private SolidcpServerRepository       $solidcpServerRepository
    ) {}

    public function handle(Command $command): void
    {
        $hostingSpace = $this->solidcpHostingSpaceRepository->get($command->id);
        $server = $this->solidcpServerRepository->get($command->id_server);

        $hostingSpace->changeServer($server);
        $this->flusher->flush($hostingSpace);
    }
}