<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Remove;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $hostingSpaceRepository;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $hostingSpaceRepository)
    {
        $this->flusher = $flusher;
        $this->hostingSpaceRepository = $hostingSpaceRepository;
    }

    public function handle(Command $command): void
    {
        $hostingSpace = $this->hostingSpaceRepository->get($command->id_hosting_space);
        $hostingSpace->removeOsTemplate($command->id_osTemplate);
        $this->flusher->flush($hostingSpace);
    }
}