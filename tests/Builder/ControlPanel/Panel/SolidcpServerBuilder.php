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
    private string $name;
    private int $cores;
    private int $threads;
    private int $memoryMb;
    private bool $enabled;

    public function __construct(
        EnterpriseDispatcher $enterprise
    )
    {
        $this->enterprise = $enterprise;
        $this->location = new Location('Test Location');
        $this->name = 'Test Server';
        $this->cores = 32;
        $this->threads = 64;
        $this->memoryMb = 1024 * 256;
        $this->enabled = true;
    }

    public function withLocation(string $name): self
    {
        $clone = clone $this;
        $clone->location = new Location($name);
        return $clone;
    }

    public function build(): SolidcpServer
    {
        return new SolidcpServer(
            $this->enterprise,
            $this->location,
            $this->name,
            $this->cores,
            $this->threads,
            $this->memoryMb,
        );
    }
}