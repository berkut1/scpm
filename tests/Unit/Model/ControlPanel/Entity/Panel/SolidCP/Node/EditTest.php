<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerEdited;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;
use App\Tests\Unit\Model\TestEnterpriseDispatcherSetupTrait;
use PHPUnit\Framework\TestCase;

final class EditTest extends TestCase
{
    use TestEnterpriseDispatcherSetupTrait;

    public function testSuccess(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))
            ->build();
        $newEnterpriseDispatcher = (new EnterpriseDispatcherBuilder($this->service))
            ->via('New ES', 'http://test.test', 'test_login2', 'test_pass2')->build();
        $location = new Location('New Edit Location');

        $solidcpServer->edit(
            $newEnterpriseDispatcher,
            $location,
            $name = 'Edited Name',
            $cores = 32,
            $threads = 64,
            $memoryMb = 1024 * 1024
        );

        self::assertSame($newEnterpriseDispatcher, $solidcpServer->getEnterprise());
        self::assertSame($location, $solidcpServer->getLocation());
        self::assertEquals($name, $solidcpServer->getName());
        self::assertEquals($cores, $solidcpServer->getCores());
        self::assertEquals($threads, $solidcpServer->getThreads());
        self::assertEquals($memoryMb, $solidcpServer->getMemoryMb());
    }

    public function testRecordEvent(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))
            ->build();

        $solidcpServer->edit(
            $solidcpServer->getEnterprise(),
            $solidcpServer->getLocation(),
            'Edited Name',
            32,
            64,
            1024 * 1024
        );

        $recordedEvents = $solidcpServer->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpServerEdited::class, $lastEvent);
    }
}