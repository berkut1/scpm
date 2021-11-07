<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\Rename;

use App\Model\ControlPanel\Entity\Package\PackageRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private PackageRepository $repository;

    public function __construct(Flusher $flusher, PackageRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $package = $this->repository->getPackage($command->id);

        if (!$package->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Package with this name already exists.');
        }
        $package->changeName($command->name);
        $this->flusher->flush($package);
    }
}