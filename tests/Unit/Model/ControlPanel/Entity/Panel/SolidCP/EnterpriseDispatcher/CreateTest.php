<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherCreated;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use PHPUnit\Framework\TestCase;

final class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $service->expects($this->once())
            ->method('getEnterpriseDispatcherRealUserId')
            ->willReturn($userId = 123);

        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))
            ->via(
                $name = 'Test Enterprise Dispatcher',
                $url = 'http://example.com',
                $login = 'test_login',
                $password = 'test_password')
            ->build();

        self::assertInstanceOf(EnterpriseDispatcher::class, $enterpriseDispatcher);
        self::assertEquals($name, $enterpriseDispatcher->getName());
        self::assertEquals($url, $enterpriseDispatcher->getUrl());
        self::assertEquals($login, $enterpriseDispatcher->getLogin());
        self::assertEquals($password, $enterpriseDispatcher->getPassword());
        self::assertEquals($userId, $enterpriseDispatcher->getSolidcpLoginId());
        self::assertFalse($enterpriseDispatcher->hasServers());
        self::assertTrue($enterpriseDispatcher->isEnabled());
    }

    public function testRecordEvent(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();

        $recordedEvents = $enterpriseDispatcher->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(EnterpriseDispatcherCreated::class, $lastEvent);
    }
}