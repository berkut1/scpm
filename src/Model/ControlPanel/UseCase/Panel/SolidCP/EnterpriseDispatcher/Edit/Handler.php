<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                        $flusher,
        private EnterpriseDispatcherRepository $repository,
        private EnterpriseDispatcherService    $enterpriseDispatcherService
    ) {}

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->repository->get($command->id);

        if (!$enterpriseDispatcher->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Enterprise Dispatcher with this name already exists.');
        }
        $headers = @get_headers($command->url);
        if (!($headers && strpos((string)$headers[0], '200'))) {
            throw new \DomainException('EnterpriseDispatcher is unreachable.');
        }

        $enterpriseDispatcher->edit($this->enterpriseDispatcherService, $command->name, $command->url, $command->login, $command->password);
        $this->flusher->flush($enterpriseDispatcher);
    }
}