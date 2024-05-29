<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\IsEnable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;

final readonly class Handler
{
    public function __construct(private EnterpriseDispatcherRepository $repository) {}

    public function handle(Command $command): bool
    {
        $enterpriseDispatcher = $this->repository->getDefaultOrById($command->id_enterprise_dispatcher_server);
        return $enterpriseDispatcher->isEnabled();
    }
}