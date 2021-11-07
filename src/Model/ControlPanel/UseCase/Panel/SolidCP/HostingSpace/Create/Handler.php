<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Create;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
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
        if ($this->solidcpHostingSpaceRepository->hasByName($command->name)) {
            throw new \DomainException('SolidcpHostingSpace with this name already exists.');
        }
        $server = $this->solidcpServerRepository->get($command->id_server);

        $solidcpHostingSpace = new SolidcpHostingSpace(
            $server,
            $command->id_hosting_space,
            $command->name,
            $command->max_active_number,
            ($command->max_reserved_memory_mb * 1024),
            $command->space_quota_gb
        );
        $this->solidcpHostingSpaceRepository->add($solidcpHostingSpace);
        $this->flusher->flush($solidcpHostingSpace);
    }
}