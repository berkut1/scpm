<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Remove;

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
        $solidcpHostingSpace = $this->repository->get($command->id);
        $solidcpHostingSpace->removeHostingPlan($command->id_plan);

        $this->flusher->flush($solidcpHostingSpace);
    }
}
