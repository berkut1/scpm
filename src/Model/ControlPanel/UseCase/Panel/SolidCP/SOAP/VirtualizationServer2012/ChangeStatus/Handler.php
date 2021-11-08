<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeStatus;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SolidCP\ServerService;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private ServerService $serverService;
    private Package\ChangeStatus\Handler $changeStatusHandler;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository, ServerService $serverService, Package\ChangeStatus\Handler $changeStatusHandler)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->serverService = $serverService;
        $this->changeStatusHandler = $changeStatusHandler;
    }

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $ip = $this->serverService->ipAddressVpsExternalNetworkDetails($enterpriseDispatcher->getId(), $command->vps_ip_address);
        if($ip['UserName'] === $enterpriseDispatcher->getLogin()){
            throw new \LogicException("You can not change status of yourself package");
        }

        $changeStatusCommand = new Package\ChangeStatus\Command($ip['PackageId'], $command->vps_status, $enterpriseDispatcher->getId());
        $this->changeStatusHandler->handle($changeStatusCommand);
    }
}
