<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Add;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository;
    private HostingPlanService $hostingPlanService;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository, HostingPlanService $hostingPlanService)
    {
        $this->flusher = $flusher;
        $this->solidcpHostingSpaceRepository = $solidcpHostingSpaceRepository;
        $this->hostingPlanService = $hostingPlanService;
    }

    public function handle(Command $command): void
    {
        $hostingSpace = $this->solidcpHostingSpaceRepository->get($command->getIdHostingSpace());
        $solidcpHostingPlan = new SolidcpHostingPlan($hostingSpace, $command->solidcp_id_plan, $this->hostingPlanService, $command->name);

        $hostingSpace->addHostingPlan($solidcpHostingPlan);
        $this->flusher->flush($hostingSpace);
    }
}