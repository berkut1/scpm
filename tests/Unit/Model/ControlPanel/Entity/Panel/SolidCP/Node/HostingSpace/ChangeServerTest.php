<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceChangedNode;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;
use App\Tests\Unit\Model\TestEnterpriseDispatcherSetupTrait;
use PHPUnit\Framework\TestCase;

final class ChangeServerTest extends TestCase
{
    use TestEnterpriseDispatcherSetupTrait;

    public function testSuccess(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->withId(10)->build();
        $hostingSpace = (new SolidcpHostingSpaceBuilder($solidcpServer, 123))
            ->build();

        $newServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->withId(11)->build();

        $hostingSpace->changeServer($newServer);
        $this->assertSame($newServer, $hostingSpace->getSolidcpServer());
    }

    public function testNothingHappens(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->withId(10)->build();
        $hostingSpace = (new SolidcpHostingSpaceBuilder($solidcpServer, 123))
            ->build();
        $hostingSpace->changeServer($solidcpServer);

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertNotInstanceOf(SolidcpHostingSpaceChangedNode::class, $lastEvent);
    }

    public function testRecordEvent(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->withId(10)->build();
        $hostingSpace = (new SolidcpHostingSpaceBuilder($solidcpServer, 123))
            ->build();
        $newServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->withId(11)->build();
        $hostingSpace->changeServer($newServer);

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceChangedNode::class, $lastEvent);
    }
}