<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;

final class SolidcpHostingSpaceAddedOsTemplate
{
    public SolidcpHostingSpace $solidcpHostingSpace;
    public OsTemplate $osTemplate;

    public function __construct(SolidcpHostingSpace $solidcpHostingSpace, OsTemplate $osTemplate)
    {
        $this->solidcpHostingSpace = $solidcpHostingSpace;
        $this->osTemplate = $osTemplate;
    }
}