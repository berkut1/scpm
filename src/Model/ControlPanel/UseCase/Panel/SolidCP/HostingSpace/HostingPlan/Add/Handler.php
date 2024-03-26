<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Add;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                       $flusher,
        private SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository,
        private HostingPlanService            $hostingPlanService
    ) {}

    public function handle(Command $command): void
    {
        $hostingSpace = $this->solidcpHostingSpaceRepository->get($command->getIdHostingSpace());
        $solidcpHostingPlan = new SolidcpHostingPlan($hostingSpace, $command->solidcp_id_plan, $this->hostingPlanService, $command->name);

        $hostingSpace->addHostingPlan($solidcpHostingPlan);
        $this->flusher->flush($hostingSpace);
    }
}