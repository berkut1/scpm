<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Package;

use App\Model\ControlPanel\Entity\Package\Event\PackageRenamed;
use App\Tests\Builder\ControlPanel\PackageBuilder;
use PHPUnit\Framework\TestCase;

final class ChangeNameTest extends TestCase
{
    public function testSuccess(): void
    {
        $package = (new PackageBuilder())->build();
        $newName = 'New Name';
        $package->changeName($newName);

        self::assertSame($newName, $package->getName());
    }

    public function testRecordEvent(): void
    {
        $package = (new PackageBuilder())->build();
        $newName = 'New Name';
        $package->changeName($newName);

        $recordedEvents = $package->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(PackageRenamed::class, $lastEvent);
    }
}