<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Disable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SolidcpServerRepository $repository;

    public function __construct(Flusher $flusher, SolidcpServerRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $solidcpServer = $this->repository->get($command->id_solidcp_server);
        $solidcpServer->disable();
        $this->flusher->flush($solidcpServer);
    }
}