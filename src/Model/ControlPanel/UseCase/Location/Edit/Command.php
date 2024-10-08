<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Edit;

use App\Model\ControlPanel\Entity\Location\Location;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?int $id;

    #[Assert\NotBlank]
    public ?string $name = null;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromLocation(Location $location): self
    {
        $command = new self($location->getId());
        $command->name = $location->getName();
        return $command;
    }
}