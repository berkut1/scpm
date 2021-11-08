<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Create;

use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpServerRepository $solidcpServerRepository;
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private LocationRepository $locationRepository;

    public function __construct(Flusher                        $flusher,
                                SolidcpServerRepository        $solidcpServerRepository,
                                EnterpriseDispatcherRepository $enterpriseDispatcherRepository,
                                LocationRepository             $locationRepository)
    {
        $this->flusher = $flusher;
        $this->solidcpServerRepository = $solidcpServerRepository;
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->locationRepository = $locationRepository;
    }

    public function handle(Command $command): void
    {
        if ($this->solidcpServerRepository->hasByName($command->name)) {
            throw new \DomainException('solidcpServer with this name already exists.');
        }
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($command->id_enterprise_dispatcher);
        $location = $this->locationRepository->get($command->id_location);

        $solidcpServer = new SolidcpServer($enterpriseDispatcher, $location, $command->name, $command->cores, $command->threads, $command->ram_mb);
        $this->solidcpServerRepository->add($solidcpServer);
        $this->flusher->flush($solidcpServer);
    }
}