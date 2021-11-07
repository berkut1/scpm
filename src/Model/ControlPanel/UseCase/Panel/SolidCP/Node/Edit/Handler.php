<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Edit;

use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpServerRepository $repository;
    private EnterpriseServerRepository $enterpriseServerRepository;
    private LocationRepository $locationRepository;

    public function __construct(Flusher $flusher,
                                SolidcpServerRepository $repository,
                                EnterpriseServerRepository $enterpriseServerRepository, LocationRepository $locationRepository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->locationRepository = $locationRepository;
    }

    public function handle(Command $command): void
    {
        $solidcpServer = $this->repository->get($command->id);
        if (!$solidcpServer->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Solidcp Server with this name already exists.');
        }
        $enterpriseServer = $this->enterpriseServerRepository->get($command->id_enterprise);
        $location = $this->locationRepository->get($command->id_location);


        $solidcpServer->edit($enterpriseServer, $location, $command->name, $command->cores, $command->threads, $command->ram_mb);
        $this->flusher->flush($solidcpServer);
    }
}