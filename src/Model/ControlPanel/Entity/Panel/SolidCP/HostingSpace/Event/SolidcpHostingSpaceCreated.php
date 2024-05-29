<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;

final class SolidcpHostingSpaceCreated
{
    public SolidcpHostingSpace $solidcpHostingSpace;

    public function __construct(SolidcpHostingSpace $solidcpHostingSpace)
    {
        $this->solidcpHostingSpace = $solidcpHostingSpace;
    }
}