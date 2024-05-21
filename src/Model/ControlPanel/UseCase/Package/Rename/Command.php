<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\Rename;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\Package;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?Id $id;

    #[Assert\NotBlank]
    public ?string $name;

    private function __construct(Id $id)
    {
        $this->id = $id;
    }

    public static function fromPackage(Package $package): self
    {
        $command = new self($package->getId());
        $command->name = $package->getName();
        return $command;
    }
}