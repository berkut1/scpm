<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $repository;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $hostingSpace = $this->repository->get($command->id);

        if (!$hostingSpace->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Hosting Space with this name already exists.');
        }
        $hostingSpace->edit($command->name, $command->max_active_number, ($command->max_reserved_memory_mb * 1024), $command->space_quota_gb);
        $this->flusher->flush($hostingSpace);
    }
}