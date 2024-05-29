<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Edit;

use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                        $flusher,
        private SolidcpServerRepository        $repository,
        private EnterpriseDispatcherRepository $enterpriseDispatcherRepository,
        private LocationRepository             $locationRepository
    ) {}

    public function handle(Command $command): void
    {
        $solidcpServer = $this->repository->get($command->id);
        if (!$solidcpServer->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Solidcp Server with this name already exists.');
        }
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($command->id_enterprise_dispatcher);
        $location = $this->locationRepository->get($command->id_location);


        $solidcpServer->edit($enterpriseDispatcher, $location, $command->name, $command->cores, $command->threads, $command->ram_mb);
        $this->flusher->flush($solidcpServer);
    }
}