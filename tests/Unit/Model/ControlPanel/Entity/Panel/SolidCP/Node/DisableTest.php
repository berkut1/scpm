<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event\SolidcpServerDisabled;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;
use App\Tests\Unit\Model\TestEnterpriseDispatcherSetupTrait;
use PHPUnit\Framework\TestCase;

final class DisableTest extends TestCase
{
    use TestEnterpriseDispatcherSetupTrait;

    public function testSuccess(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->build();
        $solidcpServer->disable();

        self::assertFalse($solidcpServer->isEnabled());
    }

    public function testAlready(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher, false))->build();
        self::expectExceptionMessage("The Node {$solidcpServer->getName()} is already disable");
        $solidcpServer->disable();
    }

    public function testRecordEvent(): void
    {
        $solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->build();
        $solidcpServer->disable();

        $recordedEvents = $solidcpServer->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpServerDisabled::class, $lastEvent);
    }
}