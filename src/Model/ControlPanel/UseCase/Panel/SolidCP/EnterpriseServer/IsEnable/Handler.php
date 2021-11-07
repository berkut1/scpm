<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\IsEnable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;

class Handler
{
    private EnterpriseServerRepository $repository;

    public function __construct(EnterpriseServerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command): bool
    {
        $enterpriseServer = $this->repository->getDefaultOrById($command->id_enterprise_server);
        return $enterpriseServer->isEnabled();
    }
}