<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\ChangeNode;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository;
    private SolidcpServerRepository $solidcpServerRepository;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository, SolidcpServerRepository $solidcpServerRepository)
    {
        $this->flusher = $flusher;
        $this->solidcpHostingSpaceRepository = $solidcpHostingSpaceRepository;
        $this->solidcpServerRepository = $solidcpServerRepository;
    }

    public function handle(Command $command): void
    {
        $hostingSpace = $this->solidcpHostingSpaceRepository->get($command->getIdHostingSpace());
        $server = $this->solidcpServerRepository->get($command->id_server);

        $hostingSpace->changeServer($server);
        $this->flusher->flush($hostingSpace);
    }
}