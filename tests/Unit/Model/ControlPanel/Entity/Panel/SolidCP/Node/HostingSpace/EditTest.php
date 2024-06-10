<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceEdited;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class EditTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->edit(
            $name = 'Edit Hosting Space',
            $maxActiveNumber = 123,
            $maxReservedMemoryKb = 123 * 1024 * 1024,
            $spaceQuotaGb = 431
        );

        self::assertSame($name, $hostingSpace->getName());
        self::assertSame($maxActiveNumber, $hostingSpace->getMaxActiveNumber());
        self::assertSame($maxReservedMemoryKb, $hostingSpace->getMaxReservedMemoryKb());
        self::assertSame($spaceQuotaGb, $hostingSpace->getSpaceQuotaGb());
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->edit(
            'Edit Hosting Space',
            123,
            123 * 1024 * 1024,
            431
        );

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceEdited::class, $lastEvent);
    }
}