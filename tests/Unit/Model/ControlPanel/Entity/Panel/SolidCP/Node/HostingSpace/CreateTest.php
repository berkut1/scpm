<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class CreateTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, $solidCpIdHostingSpace = 123))
            ->withDetails(
                $name = 'Test Hosting Space',
                $maxActiveNumber = 50,
                $maxReservedMemoryKb = 32 * 1024 * 1024,
                $spaceQuotaGb = 360)
            ->build();

        self::assertInstanceOf(SolidcpHostingSpace::class, $hostingSpace);
        self::assertSame($this->solidcpServer, $hostingSpace->getSolidcpServer());
        self::assertEquals($name, $hostingSpace->getName());
        self::assertEquals($maxActiveNumber, $hostingSpace->getMaxActiveNumber());
        self::assertEquals($maxReservedMemoryKb, $hostingSpace->getMaxReservedMemoryKb());
        self::assertEquals($spaceQuotaGb, $hostingSpace->getSpaceQuotaGb());
        self::assertEquals($solidCpIdHostingSpace, $hostingSpace->getSolidCpIdHostingSpace());
        self::assertFalse($hostingSpace->hasPlans());
        self::assertTrue($hostingSpace->isEnabled());
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))->build();

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceCreated::class, $lastEvent);
    }
}