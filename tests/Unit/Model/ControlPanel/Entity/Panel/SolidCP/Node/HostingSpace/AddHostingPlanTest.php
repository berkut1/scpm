<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\Event\SolidcpHostingPlanCreated;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingPlanBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class AddHostingPlanTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $plan = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();

        self::assertCount(0, $hostingSpace->getHostingPlans());
        $hostingSpace->addHostingPlan($plan);
        self::assertCount(1, $hostingSpace->getHostingPlans());
    }

    public function testAlready(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $plan = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();

        $hostingSpace->addHostingPlan($plan);
        $this->expectException(\DomainException::class);
        $hostingSpace->addHostingPlan($plan);
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $plan = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();

        $hostingSpace->addHostingPlan($plan);

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingPlanCreated::class, $lastEvent);
    }
}