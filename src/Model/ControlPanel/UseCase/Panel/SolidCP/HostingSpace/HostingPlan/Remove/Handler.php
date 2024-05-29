<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Remove;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                       $flusher,
        private SolidcpHostingSpaceRepository $repository
    ) {}

    public function handle(Command $command): void
    {
        $solidcpHostingSpace = $this->repository->get($command->id);
        $solidcpHostingSpace->removeHostingPlan($command->id_plan);

        $this->flusher->flush($solidcpHostingSpace);
    }
}
