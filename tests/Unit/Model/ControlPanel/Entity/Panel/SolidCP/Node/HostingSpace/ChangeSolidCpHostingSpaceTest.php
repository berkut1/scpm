<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceChangedSolidCpHostingSpaceId;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class ChangeSolidCpHostingSpaceTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->changSolidCpHostingSpace($newId = 124);
        self::assertSame($newId, $hostingSpace->getSolidCpIdHostingSpace());
    }

    public function testNothingHappens(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, $oldId = 123))
            ->build();
        $hostingSpace->changSolidCpHostingSpace($oldId);

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertNotInstanceOf(SolidcpHostingSpaceChangedSolidCpHostingSpaceId::class, $lastEvent);
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->changSolidCpHostingSpace(124);

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceChangedSolidCpHostingSpaceId::class, $lastEvent);
    }
}