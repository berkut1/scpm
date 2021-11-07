<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\ChangePlans\SolidCP;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\PackageRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\Flusher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;

class Handler
{
    private Flusher $flusher;
    private PackageRepository $packageRepository;
    private SolidcpHostingPlanFetcher $hostingPlanFetcher;

    public function __construct(Flusher $flusher, PackageRepository $packageRepository, SolidcpHostingPlanFetcher $hostingPlanFetcher)
    {
        $this->flusher = $flusher;
        $this->packageRepository = $packageRepository;
        $this->hostingPlanFetcher = $hostingPlanFetcher;
    }

    public function handle(Command $command): void
    {
        $package = $this->packageRepository->getPackage(new Id($command->getIdPackage()));
        //$plan = $this->hostingPlanFetcher->get($command->id_plan);
        //$package->assignSolidCpPlan($plan);

        $plans = array_map(function (int $id): SolidcpHostingPlan {
            return $this->hostingPlanFetcher->get($id);
        }, $command->id_plans);

        $package->changeSolidCpPlans($plans);

        $this->flusher->flush($package);
    }
}