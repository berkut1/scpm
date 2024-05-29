<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceDisabled;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class DisableTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $hostingSpace->disable();

        self::assertFalse($hostingSpace->isEnabled());
    }

    public function testAlready(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123, false))
            ->build();
        self::expectExceptionMessage("The Hosting Space {$hostingSpace->getName()} is already disable");
        $hostingSpace->disable();
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $hostingSpace->disable();

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceDisabled::class, $lastEvent);
    }
}