<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Package;

use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Tests\Builder\ControlPanel\PackageBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingPlanBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class RemoveTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $package = (new PackageBuilder())->build();
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 1))->build();
        $plan = (new SolidCPHostingPlanBuilder($hostingSpace, 1, $this->createMock(HostingPlanService::class)))->build();

        $package->assignSolidCpPlan($plan);
        $package->removeSolidCpPlan($plan);

        self::assertFalse($package->hasAssignedItems());
        self::assertNotContains($plan, $package->getSolidcpHostingPlans());
    }
}