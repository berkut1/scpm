<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Location\Edit;

use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private LocationRepository $repository;

    public function __construct(Flusher $flusher, LocationRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $location = $this->repository->get($command->id);

        if (!$location->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Location with this name already exists.');
        }
        $location->edit($command->name);
        $this->flusher->flush($location);
    }
}