<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeSolidCpHostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpHostingSpaceRepository $repository;

    public function __construct(Flusher $flusher, SolidcpHostingSpaceRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $hostingSpace = $this->repository->get($command->id);
        if(count($hostingSpace->getHostingPlans())>0){ //or can we? Who will test that?
            throw new \DomainException("You cannot change SolidCP hosting space if there are plans assigned");
        }

        $hostingSpace->changSolidCpHostingSpace($command->id_solidcp_hosting_space);
        $this->flusher->flush($hostingSpace);
    }
}