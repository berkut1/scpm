<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;

trait TestEnterpriseDispatcherSetupTrait
{
    protected EnterpriseUserValidator $service;
    protected EnterpriseDispatcher $enterpriseDispatcher;

    protected function setUp(): void
    {
        parent::setUp();
//        $this->service = $this->getMockBuilder(EnterpriseDispatcherService::class)
//            ->disableOriginalConstructor()
//            ->getMock();
        $this->service = $this->createMock(EnterpriseUserValidator::class);
        $this->service->expects($this->atLeastOnce())
            ->method('getEnterpriseDispatcherRealUserId')
            ->willReturn(123);

        $this->enterpriseDispatcher = (new EnterpriseDispatcherBuilder($this->service))->build();
    }
}