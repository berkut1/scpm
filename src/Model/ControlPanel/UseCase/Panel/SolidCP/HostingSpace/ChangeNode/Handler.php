<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeNode;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository;
    private SolidcpServerRepository $solidcpServerRepository;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository, SolidcpServerRepository $solidcpServerRepository)
    {
        $this->flusher = $flusher;
        $this->solidcpHostingSpaceRepository = $solidcpHostingSpaceRepository;
        $this->solidcpServerRepository = $solidcpServerRepository;
    }

    public function handle(Command $command): void
    {
        $hostingSpace = $this->solidcpHostingSpaceRepository->get($command->id);
        if(count($hostingSpace->getHostingPlans())>0){ //or can we? Who will test that?
            throw new \DomainException("You cannot change Host/Server if there are plans assigned");
        }
        $server = $this->solidcpServerRepository->get($command->id_server);

        $hostingSpace->changeServer($server);
        $this->flusher->flush($hostingSpace);
    }
}