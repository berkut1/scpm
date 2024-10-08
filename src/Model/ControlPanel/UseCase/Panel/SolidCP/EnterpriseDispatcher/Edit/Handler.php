<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Model\Flusher;
use App\Service\CustomHttpClientInterface;

final readonly class Handler
{
    public function __construct(
        private Flusher                        $flusher,
        private EnterpriseDispatcherRepository $repository,
        private EnterpriseUserValidator        $enterpriseDispatcherService,
        private CustomHttpClientInterface      $customHttpClient,
    ) {}

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->repository->get($command->id);

        if (!$enterpriseDispatcher->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Enterprise Dispatcher with this name already exists.');
        }
        $headers = $this->customHttpClient->getHeaders($command->url);
        if (empty($headers) || !strpos((string)$headers[0], '200')) {
            throw new \DomainException('EnterpriseDispatcher is unreachable.');
        }
        $command->url = $this->removeTrailingSlash($command->url);

        $enterpriseDispatcher->edit($this->enterpriseDispatcherService, $command->name, $command->url, $command->login, $command->password);
        $this->flusher->flush($enterpriseDispatcher);
    }

    private function removeTrailingSlash(string $url): string
    {
        $url = trim($url);
        if (str_ends_with($url, '/')) {
            $url = substr($url, 0, -1);
        }

        return $url;
    }
}