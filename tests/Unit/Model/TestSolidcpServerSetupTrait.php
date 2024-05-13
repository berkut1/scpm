<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;

trait TestSolidcpServerSetupTrait
{
    protected EnterpriseDispatcherService $service;
    protected EnterpriseDispatcher $enterpriseDispatcher;
    protected SolidcpServer $solidcpServer;

    protected function setUp(): void
    {
        parent::setUp();

//        $this->service = $this->getMockBuilder(EnterpriseDispatcherService::class)
//            ->disableOriginalConstructor()
//            ->getMock();
        $this->service = $this->createMock(EnterpriseDispatcherService::class);
        $this->service->expects($this->once())
            ->method('getEnterpriseDispatcherRealUserId')
            ->willReturn(123);

        $this->enterpriseDispatcher = (new EnterpriseDispatcherBuilder($this->service))->build();
        $this->solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->build();
    }
}