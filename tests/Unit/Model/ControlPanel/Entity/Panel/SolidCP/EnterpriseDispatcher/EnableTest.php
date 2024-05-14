<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherEnabled;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use PHPUnit\Framework\TestCase;

final class EnableTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = $this->createMock(EnterpriseDispatcherService::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();
        $enterpriseDispatcher->disable();
        $enterpriseDispatcher->enable();

        self::assertTrue($enterpriseDispatcher->isEnabled());
    }

    public function testAlready(): void
    {
        $service = $this->createMock(EnterpriseDispatcherService::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();

        self::expectExceptionMessage("The Enterprise Dispatcher {$enterpriseDispatcher->getName()} is already enable");
        $enterpriseDispatcher->enable();
    }

    public function testRecordEvent(): void
    {
        $service = $this->createMock(EnterpriseDispatcherService::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();
        $enterpriseDispatcher->disable();
        $enterpriseDispatcher->enable();

        $recordedEvents = $enterpriseDispatcher->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(EnterpriseDispatcherEnabled::class, $lastEvent);
    }
}