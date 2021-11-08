<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\IsEnable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;

class Handler
{
    private EnterpriseDispatcherRepository $repository;

    public function __construct(EnterpriseDispatcherRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command): bool
    {
        $enterpriseDispatcher = $this->repository->getDefaultOrById($command->id_enterprise_dispatcher_server);
        return $enterpriseDispatcher->isEnabled();
    }
}