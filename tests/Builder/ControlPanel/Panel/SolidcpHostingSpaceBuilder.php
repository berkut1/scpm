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
    private ?int $id = null;

    public function __construct(
        SolidcpServer $solidcpServer,
        int           $solidCpIdHostingSpace,
        bool          $enabled = true,
    )
    {
        $this->solidcpServer = $solidcpServer;
        $this->solidCpIdHostingSpace = $solidCpIdHostingSpace;
        $this->name = 'Test Hosting Space';
        $this->maxActiveNumber = 70;
        $this->maxReservedMemoryKb = 1024 * 1024 * 64;
        $this->spaceQuotaGb = 500;
        $this->enabled = $enabled;
    }

    public function withDetails(string $name, int $maxActiveNumber, int $maxReservedMemoryKb, int $spaceQuotaGb): self
    {
        $clone = clone $this;
        $clone->name = $name;
        $clone->maxActiveNumber = $maxActiveNumber;
        $clone->maxReservedMemoryKb = $maxReservedMemoryKb;
        $clone->spaceQuotaGb = $spaceQuotaGb;
        return $clone;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function build(): SolidcpHostingSpace
    {
        $entity = new SolidcpHostingSpace(
            $this->solidcpServer,
            $this->solidCpIdHostingSpace,
            $this->name,
            $this->maxActiveNumber,
            $this->maxReservedMemoryKb,
            $this->spaceQuotaGb,
            $this->enabled,
        );

        if ($this->id !== null) {
            $reflection = new \ReflectionClass($entity);
            $property = $reflection->getProperty('id');
            $property->setValue($entity, $this->id);
        }

        return $entity;
    }
}