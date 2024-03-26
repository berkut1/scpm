<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Enable;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                        $flusher,
        private EnterpriseDispatcherRepository $repository
    ) {}

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->repository->get($command->id_enterprise_dispatcher_server);
        $enterpriseDispatcher->enable();
        $this->flusher->flush($enterpriseDispatcher);
    }
}