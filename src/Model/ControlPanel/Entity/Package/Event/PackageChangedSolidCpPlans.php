<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package\Event;

use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;

final class PackageChangedSolidCpPlans
{
    public Package $package;
    /**
     * @var SolidcpHostingPlan[]
     */
    public array $removedPlans;
    /**
     * @var SolidcpHostingPlan[]
     */
    public array $addedPlans;

    /**
     * @param SolidcpHostingPlan[] $removedPlans
     * @param SolidcpHostingPlan[] $addedPlans
     */
    public function __construct(Package $package, array $removedPlans, array $addedPlans)
    {
        $this->package = $package;
        $this->removedPlans = $removedPlans;
        $this->addedPlans = $addedPlans;
    }
}