<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package\Event;

use App\Model\ControlPanel\Entity\Package\Package;

final class PackageRenamed
{
    public Package $package;
    public string $oldName;

    public function __construct(Package $package, string $oldName)
    {
        $this->package = $package;
        $this->oldName = $oldName;
    }
}