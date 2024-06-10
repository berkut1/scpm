<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;

final class SolidcpHostingPlanBuilder
{
    private SolidcpHostingSpace $hostingSpace;
    private int $solidcpIdPlan;
    private HostingPlanService $hostingPlanService;
    private string $name = 'Test Plan';
    private int $id = 1;

    public function __construct(SolidcpHostingSpace $hostingSpace, int $solidcpIdPlan, HostingPlanService $hostingPlanService)
    {
        $this->hostingSpace = $hostingSpace;
        $this->solidcpIdPlan = $solidcpIdPlan;
        $this->hostingPlanService = $hostingPlanService;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function build(): SolidcpHostingPlan
    {
        $solidcpHostingPlan = new SolidcpHostingPlan(
            $this->hostingSpace,
            $this->solidcpIdPlan,
            $this->hostingPlanService,
            $this->name
        );

        $reflection = new \ReflectionClass($solidcpHostingPlan);
        $property = $reflection->getProperty('id');
        $property->setValue($solidcpHostingPlan, $this->id);

        return $solidcpHostingPlan;
    }
}