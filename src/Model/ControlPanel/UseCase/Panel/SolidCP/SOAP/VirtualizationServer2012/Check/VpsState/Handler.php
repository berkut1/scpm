<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsState;

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
        $result = $esUsers->getVirtualMachineGeneralDetails($command->solidcp_item_id);
//        dump($result);

        return $result['State'];
    }
}