<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseServerService;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseServerRepository $repository;
    private EnterpriseServerService $enterpriseServerService;

    public function __construct(Flusher $flusher, EnterpriseServerRepository $repository, EnterpriseServerService $enterpriseServerService)
    {
        $this->flusher = $flusher;
        $this->repository = $repository;
        $this->enterpriseServerService = $enterpriseServerService;
    }

    public function handle(Command $command): void
    {
        $enterpriseServer = $this->repository->get($command->id);

        if (!$enterpriseServer->isEqualName($command->name) && $this->repository->hasByName($command->name)) {
            throw new \DomainException('Enterprise Server with this name already exists.');
        }
        $headers = @get_headers($command->url);
        if (!($headers && strpos($headers[0], '200'))) {
            throw new \DomainException('EnterpriseServer is unreachable.');
        }

        $enterpriseServer->edit($this->enterpriseServerService, $command->name, $command->url, $command->login, $command->password);
        $this->flusher->flush($enterpriseServer);
    }
}