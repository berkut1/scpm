<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Disable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseServerRepository $repository;

    public function __construct(Flusher $flusher, EnterpriseServerRepository $repository)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $enterpriseServer = $this->repository->get($command->id_enterprise_server);
        $enterpriseServer->disable();
        $this->flusher->flush($enterpriseServer);
    }
}