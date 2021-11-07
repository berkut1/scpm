<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\SetDefault;

use App\Model\Flusher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingPlanFetcher $solidcpHostingPlanFetcher;

    public function __construct(Flusher $flusher, SolidcpHostingPlanFetcher $solidcpHostingPlanFetcher)
    {
        $this->flusher = $flusher;
        $this->solidcpHostingPlanFetcher = $solidcpHostingPlanFetcher;
    }

    public function handle(Command $command): void
    {
        if($defaultPlan = $this->solidcpHostingPlanFetcher->getDefault()){
            $defaultPlan->setNonDefault();
            //$this->flusher->flush();
        }

        $plan = $this->solidcpHostingPlanFetcher->get($command->id);
        $plan->setDefault();

        $this->flusher->flush($defaultPlan, $plan);
    }
}
