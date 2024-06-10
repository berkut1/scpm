<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Package\VirtualMachine;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\Event\VirtualMachinePackageEdited;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;
use PHPUnit\Framework\TestCase;

final class EditTest extends TestCase
{
    public function testSuccess(): void
    {
        $vmPackage = new VirtualMachinePackage(
            Id::next(),
            'VM test package',
            2,
            2,
            3072,
            70,
            0,
            200
        );
        $newRamMb = 2048;
        $spaceGb = 40;
        $IopsMax = 40;
        $vmPackage->edit(1, 2, $newRamMb, $spaceGb, 0, $IopsMax);

        self::assertSame($newRamMb, $vmPackage->getRamMb());
        self::assertSame($spaceGb, $vmPackage->getSpaceGb());
        self::assertSame($IopsMax, $vmPackage->getIopsMax());
    }

    public function testRecordEvent(): void
    {
        $vmPackage = new VirtualMachinePackage(
            Id::next(),
            'VM test package',
            2,
            2,
            3072,
            70,
            0,
            200
        );
        $newRamMb = 2048;
        $spaceGb = 40;
        $IopsMax = 40;
        $vmPackage->edit(1, 2, $newRamMb, $spaceGb, 0, $IopsMax);

        $recordedEvents = $vmPackage->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(VirtualMachinePackageEdited::class, $lastEvent);
    }
}