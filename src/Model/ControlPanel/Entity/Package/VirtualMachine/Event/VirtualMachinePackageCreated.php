<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package\VirtualMachine\Event;

use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;

final class VirtualMachinePackageCreated
{
    public VirtualMachinePackage $virtualMachinePackage;

    public function __construct(VirtualMachinePackage $virtualMachinePackage)
    {
        $this->virtualMachinePackage = $virtualMachinePackage;
    }
}