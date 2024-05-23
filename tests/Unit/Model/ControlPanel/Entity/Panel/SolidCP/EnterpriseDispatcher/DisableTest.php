<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherDisabled;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use PHPUnit\Framework\TestCase;

final class DisableTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();
        $enterpriseDispatcher->disable();

        self::assertFalse($enterpriseDispatcher->isEnabled());
    }

    public function testAlready(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();
        $enterpriseDispatcher->disable();

        self::expectExceptionMessage("The Enterprise Dispatcher {$enterpriseDispatcher->getName()} is already disable");
        $enterpriseDispatcher->disable();
    }

    public function testRecordEvent(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();
        $enterpriseDispatcher->disable();

        $recordedEvents = $enterpriseDispatcher->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(EnterpriseDispatcherDisabled::class, $lastEvent);
    }
}