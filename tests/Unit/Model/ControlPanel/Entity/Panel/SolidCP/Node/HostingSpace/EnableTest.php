<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceEnabled;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class EnableTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123, false))
            ->build();
        $hostingSpace->enable();

        self::assertTrue($hostingSpace->isEnabled());
    }

    public function testAlready(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123, true))
            ->build();
        self::expectExceptionMessage("The Hosting Space {$hostingSpace->getName()} is already enable");
        //$this->expectException(\DomainException::class);
        $hostingSpace->enable();
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123, false))
            ->build();
        $hostingSpace->enable();

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceEnabled::class, $lastEvent);
    }
}