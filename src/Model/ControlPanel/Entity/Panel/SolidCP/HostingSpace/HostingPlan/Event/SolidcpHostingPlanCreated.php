<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;

final class SolidcpHostingPlanCreated
{
    public SolidcpHostingPlan $solidcpHostingPlan;

    public function __construct(SolidcpHostingPlan $solidcpHostingPlan)
    {
        $this->solidcpHostingPlan = $solidcpHostingPlan;
    }
}