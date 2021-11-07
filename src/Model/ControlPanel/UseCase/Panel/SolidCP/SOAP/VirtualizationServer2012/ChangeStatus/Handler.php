<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeStatus;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\SolidCP\ServerService;

class Handler
{
    private EnterpriseServerRepository $enterpriseServerRepository;
    private ServerService $serverService;
    private Package\ChangeStatus\Handler $changeStatusHandler;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository, ServerService $serverService, Package\ChangeStatus\Handler $changeStatusHandler)
    {
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->serverService = $serverService;
        $this->changeStatusHandler = $changeStatusHandler;
    }

    public function handle(Command $command): void
    {
        $enterpriseServer = $this->enterpriseServerRepository->getDefaultOrById($command->id_enterprise);

        $ip = $this->serverService->ipAddressVpsExternalNetworkDetails($enterpriseServer->getId(), $command->vps_ip_address);
        if($ip['UserName'] === $enterpriseServer->getLogin()){
            throw new \LogicException("You can not change status of yourself package");
        }

        $changeStatusCommand = new Package\ChangeStatus\Command($ip['PackageId'], $command->vps_status, $enterpriseServer->getId());
        $this->changeStatusHandler->handle($changeStatusCommand);
    }
}
