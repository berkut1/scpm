<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Remove;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                       $flusher,
        private SolidcpHostingSpaceRepository $hostingSpaceRepository
    ) {}

    public function handle(Command $command): void
    {
        $hostingSpace = $this->hostingSpaceRepository->get($command->id_hosting_space);
        $hostingSpace->removeOsTemplate($command->id_osTemplate);
        $this->flusher->flush($hostingSpace);
    }
}