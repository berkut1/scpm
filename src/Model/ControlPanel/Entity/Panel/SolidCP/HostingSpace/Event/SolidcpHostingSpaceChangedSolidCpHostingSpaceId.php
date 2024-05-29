<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;

final class SolidcpHostingSpaceChangedSolidCpHostingSpaceId
{
    public SolidcpHostingSpace $solidcpHostingSpace;
    public int $oldSolidCpHostingSpaceId;
    public int $newSolidCpHostingSpaceId;

    public function __construct(SolidcpHostingSpace $solidcpHostingSpace, int $oldSolidCpHostingSpaceId, int $newSolidCpHostingSpaceId)
    {

        $this->solidcpHostingSpace = $solidcpHostingSpace;
        $this->oldSolidCpHostingSpaceId = $oldSolidCpHostingSpaceId;
        $this->newSolidCpHostingSpaceId = $newSolidCpHostingSpaceId;
    }
}