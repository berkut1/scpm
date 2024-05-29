<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Location\Event;

use App\Model\ControlPanel\Entity\Location\Location;

final class LocationRenamed
{
    public string $oldName;
    public Location $location;

    public function __construct(string $oldName, Location $location)
    {
        $this->oldName = $oldName;
        $this->location = $location;
    }
}