<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Create;

use App\Model\ControlPanel\Entity\Location\LocationRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpServerRepository $solidcpServerRepository;
    private EnterpriseServerRepository $enterpriseServerRepository;
    private LocationRepository $locationRepository;

    public function __construct(Flusher $flusher,
                                SolidcpServerRepository $solidcpServerRepository,
                                EnterpriseServerRepository $enterpriseServerRepository,
                                LocationRepository $locationRepository)
    {
        $this->flusher = $flusher;
        $this->solidcpServerRepository = $solidcpServerRepository;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->locationRepository = $locationRepository;
    }

    public function handle(Command $command): void
    {
        if ($this->solidcpServerRepository->hasByName($command->name)) {
            throw new \DomainException('solidcpServer with this name already exists.');
        }
        $enterpriseServer = $this->enterpriseServerRepository->get($command->id_enterprise);
        $location = $this->locationRepository->get($command->id_location);

        $solidcpServer = new SolidcpServer($enterpriseServer, $location, $command->name, $command->cores, $command->threads, $command->ram_mb);
        $this->solidcpServerRepository->add($solidcpServer);
        $this->flusher->flush($solidcpServer);
    }
}