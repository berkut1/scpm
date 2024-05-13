<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Package;

use App\Model\ControlPanel\Entity\Package\Event\PackageChangedSolidCpPlans;
use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Tests\Builder\ControlPanel\PackageBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingPlanBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class ChangePlanTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $package = (new PackageBuilder())->build();
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 1))->build();
        $plan1 = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();
        $plan2 = (new SolidCPHostingPlanBuilder($hostingSpace, 2, $this->createMock(HostingPlanService::class)))->withId(2)->build();
        $plan3 = (new SolidCPHostingPlanBuilder($hostingSpace, 3, $this->createMock(HostingPlanService::class)))->withId(3)->build();

//        $plan1 = $this->plan($package, 1);
//        $plan2 = $this->plan($package, 2);
//        $plan3 = $this->plan($package, 3);

        $package->assignSolidCpPlan($plan1);
        $package->assignSolidCpPlan($plan2);
        $package->changeSolidCpPlans([$plan2, $plan3]);

        self::assertCount(2, $package->getSolidcpHostingPlans());
        self::assertContains($plan2, $package->getSolidcpHostingPlans());
        self::assertContains($plan3, $package->getSolidcpHostingPlans());
    }

    public function testRecordEvent(): void
    {
        $package = (new PackageBuilder())->build();
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 1))->build();
        $plan1 = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->withId(1)->build();
        $plan2 = (new SolidCPHostingPlanBuilder($hostingSpace, 2, $this->createMock(HostingPlanService::class)))->withId(2)->build();

        $package->changeSolidCpPlans([$plan1, $plan2]);

        $recordedEvents = $package->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(PackageChangedSolidCpPlans::class, $lastEvent);
    }

    private function plan(Package $package, int $id): SolidcpHostingPlan
    {
        $object = $this->createMock(SolidcpHostingPlan::class);
        $object->expects(self::once())
            ->method('assignPackage')
            ->with(self::equalTo($package));
        $object->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn($id);
        return $object;
    }
}