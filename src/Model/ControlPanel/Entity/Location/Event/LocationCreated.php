<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Location\Event;

final class LocationCreated
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}