<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;

final class SolidcpServerBuilder
{
    private EnterpriseDispatcher $enterprise;
    private Location $location;
    private string $name = 'Test Server';
    private int $cores = 32;
    private int $threads = 64;
    private int $memoryMb;
    private bool $enabled;
    private ?int $id = null;

    public function __construct(
        EnterpriseDispatcher $enterprise, bool $enabled = true
    )
    {
        $this->enterprise = $enterprise;
        $this->location = new Location('Test Location');
        $this->memoryMb = 1024 * 256;
        $this->enabled = $enabled;
    }

    public function withServerDetails(string $name, int $cores, int $threads, int $memoryMb): self
    {
        $clone = clone $this;
        $clone->name = $name;
        $clone->cores = $cores;
        $clone->threads = $threads;
        $clone->memoryMb = $memoryMb;
        return $clone;
    }

    public function withLocation(Location $location): self
    {
        $clone = clone $this;
        $clone->location = $location;
        return $clone;
    }

    public function withNewLocation(string $name): self
    {
        $clone = clone $this;
        $clone->location = new Location($name);
        return $clone;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function build(): SolidcpServer
    {
        $entity = new SolidcpServer(
            $this->enterprise,
            $this->location,
            $this->name,
            $this->cores,
            $this->threads,
            $this->memoryMb,
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