<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\ControlPanel\Entity\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\Event\SolidcpHostingSpaceRemovedOsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplate;
use App\Model\EntityNotFoundException;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Unit\Model\TestSolidcpServerSetupTrait;
use PHPUnit\Framework\TestCase;

final class RemoveOsTemplateTest extends TestCase
{
    use TestSolidcpServerSetupTrait;

    public function testSuccess(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->addOsTemplate('template.vhdx', $name = 'Test Template');
        $template = $hostingSpace->getOsTemplateByName($name);

        $this->initId($template, $id = 12);

        $hostingSpace->removeOsTemplate($id);
        self::assertCount(0, $hostingSpace->getOsTemplates());
    }

    public function testNotFound(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();
        $this->expectException(EntityNotFoundException::class);
        $hostingSpace->removeOsTemplate(99999);
    }

    public function testRecordEvent(): void
    {
        $hostingSpace = (new SolidcpHostingSpaceBuilder($this->solidcpServer, 123))
            ->build();

        $hostingSpace->addOsTemplate('template.vhdx', $name = 'Test Template');
        $template = $hostingSpace->getOsTemplateByName($name);

        $this->initId($template, $id = 12);

        $hostingSpace->removeOsTemplate($id);

        $recordedEvents = $hostingSpace->releaseEvents();
        $lastEvent = end($recordedEvents);
        self::assertInstanceOf(SolidcpHostingSpaceRemovedOsTemplate::class, $lastEvent);
    }

    private function initId(OsTemplate $template, $id): void
    {
        $reflection = new \ReflectionClass($template);
        $property = $reflection->getProperty('id');
        $property->setValue($template, $id);
    }
}