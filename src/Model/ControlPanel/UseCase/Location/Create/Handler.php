<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Create;

use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(private LocationRepository $locationRepository, private Flusher $flusher) {}

    public function handle(Command $command): void
    {
        if ($this->locationRepository->hasByName($command->name)) {
            throw new \DomainException('Location with this name already exists.');
        }
        $location = new Location($command->name);
        $this->locationRepository->add($location);
        $this->flusher->flush($location);
    }
}