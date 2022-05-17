<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsState\ByIP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;
use App\Model\ControlPanel\Service\SolidCP\ServerService;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private ServerService $serverService;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository, ServerService $serverService)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->serverService = $serverService;
    }

    public function handle(Command $command): string
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        $ip = $this->serverService->ipAddressVpsExternalNetworkDetails($enterpriseDispatcher->getId(), $command->vps_ip_address);

        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $result = $esVirtualizationServer2012->getVirtualMachineGeneralDetails($ip['ItemId']);

        return $result['State'];
    }
}