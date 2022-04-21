<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\Rename;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\Package;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public Id $id;

    #[Assert\NotBlank]
    public string $name;

    private function __construct(Id $id)
    {
        $this->id = $id;
    }

    #[Pure]
    public static function fromPackage(Package $package): self
    {
        $command = new self($package->getId());
        $command->name = $package->getName();
        return $command;
    }
}