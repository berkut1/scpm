<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;

final class SolidcpHostingSpaceRemovedPlan
{
    public SolidcpHostingSpace $solidcpHostingSpace;
    public string $planName;

    public function __construct(SolidcpHostingSpace $solidcpHostingSpace, string $planName)
    {
        $this->solidcpHostingSpace = $solidcpHostingSpace;
        $this->planName = $planName;
    }
}