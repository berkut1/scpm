<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;

final class SolidcpHostingSpaceChangedNode
{
    public SolidcpHostingSpace $solidcpHostingSpace;
    public string $oldNodeName;
    public string $newNodeName;

    public function __construct(SolidcpHostingSpace $solidcpHostingSpace, string $oldNodeName, string $newNodeName)
    {
        $this->solidcpHostingSpace = $solidcpHostingSpace;
        $this->oldNodeName = $oldNodeName;
        $this->newNodeName = $newNodeName;
    }
}