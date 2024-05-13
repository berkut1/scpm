<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;

final class SolidcpHostingSpaceBuilder
{
    private SolidcpServer $solidcpServer;
    private int $solidCpIdHostingSpace;
    private string $name;
    private int $maxActiveNumber;
    private int $maxReservedMemoryKb;
    private int $spaceQuotaGb;
    private bool $enabled;

    public function __construct(
        SolidcpServer $solidcpServer,
        int           $solidCpIdHostingSpace,
    )
    {
        $this->solidcpServer = $solidcpServer;
        $this->solidCpIdHostingSpace = $solidCpIdHostingSpace;
        $this->name = 'Test Hosting Space';
        $this->maxActiveNumber = 70;
        $this->maxReservedMemoryKb = 1024 * 1024 * 64;
        $this->spaceQuotaGb = 500;
        $this->enabled = true;
    }

    public function build(): SolidcpHostingSpace
    {
        return new SolidcpHostingSpace(
            $this->solidcpServer,
            $this->solidCpIdHostingSpace,
            $this->name,
            $this->maxActiveNumber,
            $this->maxReservedMemoryKb,
            $this->spaceQuotaGb,
        );
    }
}