<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerEnabled;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;
use App\Tests\Unit\Model\TestEnterpriseDispatcherSetupTrait;
use PHPUnit\Framework\TestCase;

final class EnableTest extends TestCase
{
    use TestEnterpriseDispatcherSetupTrait;

    public function testSuccess(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher, false))->build();
        $solidcpServer->enable();

        self::assertTrue($solidcpServer->isEnabled());
    }

    public function testAlready(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->build();
        self::expectExceptionMessage("The Node {$solidcpServer->getName()} is already enable");
        $solidcpServer->enable();
    }

    public function testRecordEvent(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher, false))->build();
        $solidcpServer->enable();

        $recordedEvents = $solidcpServer->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpServerEnabled::class, $lastEvent);
    }
}