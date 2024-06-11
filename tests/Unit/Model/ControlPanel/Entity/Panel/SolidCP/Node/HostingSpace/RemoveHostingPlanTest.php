<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceRemovedPlan;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Model\EntityNotFoundException;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingPlanBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class RemoveHostingPlanTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $plan = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();

        $hostingSpace->addHostingPlan($plan);
        $hostingSpace->removeHostingPlan($plan->getId());

        self::assertCount(0, $hostingSpace->getHostingPlans());
    }

    public function testNotFound(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $this->expectException(EntityNotFoundException::class);
        $hostingSpace->removeHostingPlan(99999);
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $plan = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();

        $hostingSpace->addHostingPlan($plan);
        $hostingSpace->removeHostingPlan($plan->getId());

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceRemovedPlan::class, $lastEvent);
    }
}