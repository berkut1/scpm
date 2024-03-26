<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Create;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private EnterpriseDispatcherRepository $enterpriseDispatcherRepository,
        private Flusher                        $flusher,
        private EnterpriseDispatcherService    $enterpriseDispatcherService
    ) {}

    public function handle(Command $command): void
    {
        if ($this->enterpriseDispatcherRepository->hasByName($command->name)) {
            throw new \DomainException('EnterpriseDispatcher with this name already exists.');
        }
        $headers = @get_headers($command->url);
        if (!($headers && strpos((string)$headers[0], '200'))) {
            throw new \DomainException('EnterpriseDispatcher is unreachable.');
        }

        $enterpriseDispatcher = new EnterpriseDispatcher($this->enterpriseDispatcherService, $command->name, $command->url, $command->login, $command->password);
        if ($this->enterpriseDispatcherRepository->getDefaultOrNull() === null) {
            $enterpriseDispatcher->setDefault();
        }

        $this->enterpriseDispatcherRepository->add($enterpriseDispatcher);
        $this->flusher->flush($enterpriseDispatcher);
    }
}