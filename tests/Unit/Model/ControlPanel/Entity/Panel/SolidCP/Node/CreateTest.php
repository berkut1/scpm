<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerCreated;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;
use App\Tests\Unit\Model\TestEnterpriseDispatcherSetupTrait;
use PHPUnit\Framework\TestCase;

final class CreateTest extends TestCase
{
    use TestEnterpriseDispatcherSetupTrait;

    public function testSuccess(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))
            ->withLocation($locName = 'Test Node Location')
            ->withServerDetails($name = 'Test Node', $cores = 16, $threads = 32, $memoryMb = 1024 * 512)
            ->build();

        self::assertInstanceOf(SolidcpServer::class, $solidcpServer);
        self::assertSame($this->enterpriseDispatcher, $solidcpServer->getEnterprise());
        self::assertEquals($name, $solidcpServer->getName());
        self::assertEquals($cores, $solidcpServer->getCores());
        self::assertEquals($threads, $solidcpServer->getThreads());
        self::assertEquals($memoryMb, $solidcpServer->getMemoryMb());
        self::assertEquals($locName, $solidcpServer->getLocation()->getName());
        self::assertFalse($solidcpServer->hasHostingSpace());
        self::assertTrue($solidcpServer->isEnabled());
    }

    public function testRecordEvent(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->build();

        $recordedEvents = $solidcpServer->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpServerCreated::class, $lastEvent);
    }
}