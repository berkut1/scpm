<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Package\PackageType;

final class PackageBuilder
{
    private string $name;
    private readonly PackageType $packageType;

    public function __construct() {
        $this->name = 'Test VM Package';
        $this->packageType = PackageType::virtualMachine();
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function build(): Package
    {
        return new Package(
            Id::next(),
            $this->name,
            $this->packageType
        );
    }
}