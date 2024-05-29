<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceAddedOsTemplate;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class AddOsTemplateTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->addOsTemplate('template.vhdx', 'Test Template');

        $this->assertCount(1, $hostingSpace->getOsTemplates());
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->addOsTemplate('template.vhdx', 'Test Template');

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceAddedOsTemplate::class, $lastEvent);
    }
}