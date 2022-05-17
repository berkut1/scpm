<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsState\ByItemId;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
    }

    public function handle(Command $command): string
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        $esUsers = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $result = $esUsers->getVirtualMachineGeneralDetails($command->solidcp_item_id);

        return $result['State'];
    }
}