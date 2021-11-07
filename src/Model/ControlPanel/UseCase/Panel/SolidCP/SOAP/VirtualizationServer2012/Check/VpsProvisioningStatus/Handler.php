<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsProvisioningStatus;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;

class Handler
{
    private EnterpriseServerRepository $enterpriseServerRepository;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository)
    {
        $this->enterpriseServerRepository = $enterpriseServerRepository;
    }

    public function handle(Command $command): string
    {
        $enterpriseServer = $this->enterpriseServerRepository->getDefaultOrById($command->id_enterprise);
        $esUsers = EsVirtualizationServer2012::createFromEnterpriseServer($enterpriseServer);
        $result = $esUsers->getVirtualMachineItem($command->solidcp_item_id);
        $provisioningStatus = $result['ProvisioningStatus'];
        if(!empty($result['CurrentTaskId'] ) && $result['ProvisioningStatus'] === 'Error'){ //can get ERROR before OK status (that how works solidcp)
            $provisioningStatus = 'InProgress';
        }

        return $provisioningStatus;
    }
}