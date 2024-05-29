<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingPlanBuilder;
use App\Tests\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class HostingPlanFixture extends Fixture implements DependentFixtureInterface
{
    public const int EXISTING_ID = 1000001;
    public const int EXISTING_SOLIDCP_PLAN_ID = 2000001;
    public const string REFERENCE = 'hosting_plan';

    public function __construct()
    {
        //if renew a cache, then need to run double time fixture (first time will be exception)
        \DG\BypassFinals::setCacheDirectory('\var\cache');
        \DG\BypassFinals::enable(); //We can put in bin/console.php before kernel load, but that will enable it for all commands, so it's probably not a good idea.
    }

    public function load(ObjectManager $manager): void
    {
        $hostingSpace = $this->getReference(HostingSpaceFixture::REFERENCES[HostingSpaceFixture::EXISTING_ID_ENABLED]);

        $hostingSpace = (new SolidcpHostingPlanBuilder($hostingSpace, self::EXISTING_SOLIDCP_PLAN_ID, $this->mockHostingPlanServiceWith($hostingSpace, self::EXISTING_SOLIDCP_PLAN_ID)))
            ->withName('Exist Hosting Plan')
            ->withId(self::EXISTING_ID)
            ->build();
        $manager->persist($hostingSpace);
        $this->setReference(self::REFERENCE, $hostingSpace);

        Utils::flushEntityWithCustomId($manager, SolidcpHostingPlan::class);
    }

    private function mockHostingPlanServiceWith(SolidcpHostingSpace $hostingSpace, int $plan_id): HostingPlanService
    {
        $hostingPlanService = \Mockery::mock(HostingPlanService::class);
        $hostingPlanService->shouldReceive('getRealSolidCpServerIdFromPlanId')->with($hostingSpace, $plan_id)->andReturn(99999);
        return $hostingPlanService;
    }

    public function __destruct()
    {
        \Mockery::close();
    }

    public function getDependencies(): array
    {
        return [
            HostingSpaceFixture::class,
        ];
    }
}