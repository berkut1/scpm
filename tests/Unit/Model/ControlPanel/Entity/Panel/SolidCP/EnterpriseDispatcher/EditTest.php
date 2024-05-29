<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event\EnterpriseDispatcherEdited;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use PHPUnit\Framework\TestCase;

final class EditTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();

        $newService = $this->createMock(EnterpriseUserValidator::class);
        $newService->expects($this->once())
            ->method('getEnterpriseDispatcherRealUserId')
            ->willReturn($userId = 321);

        $enterpriseDispatcher->edit(
            $newService,
            $name = 'Updated Enterprise Dispatcher',
            $url = 'http://updated-example.com',
            $login = 'http://updated-example.com',
            $password = 'updated_test_password');

        self::assertEquals($name, $enterpriseDispatcher->getName());
        self::assertEquals($url, $enterpriseDispatcher->getUrl());
        self::assertEquals($login, $enterpriseDispatcher->getLogin());
        self::assertEquals($password, $enterpriseDispatcher->getPassword());
        self::assertEquals($userId, $enterpriseDispatcher->getSolidcpLoginId());
    }

    public function testRecordEvent(): void
    {
        $service = $this->createMock(EnterpriseUserValidator::class);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($service))->build();

        $enterpriseDispatcher->edit(
            $service,
            $enterpriseDispatcher->getName(),
            $enterpriseDispatcher->getUrl(),
            $enterpriseDispatcher->getLogin(),
            'updated_test_password');

        $recordedEvents = $enterpriseDispatcher->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(EnterpriseDispatcherEdited::class, $lastEvent);
    }
}