<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsProvisioningStatus;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsTasks;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;
use JetBrains\PhpStorm\ArrayShape;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
    }

    #[ArrayShape([
        'ProvisioningStatus' => "string",
        'task' => "array",
    ])]
    public function handle(Command $command): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $result = $esVirtualizationServer2012->getVirtualMachineItem($command->solidcp_item_id);
        $provisioningStatus = [
            'ProvisioningStatus' => $result['ProvisioningStatus'],
            'task' => [
                'PercentComplete' => 2,
                'CreationTime' => $result['CreationTime'],
                'Status' => null,
            ],
        ];

        if(!empty($result['CurrentTaskId'])){
            $esTask = EsTasks::createFromEnterpriseDispatcher($enterpriseDispatcher);
            $taskResult = $esTask->getTask($result['CurrentTaskId']);
            $PercentComplete = $taskResult['IndicatorCurrent']; //the earliest value can be -1 or 0, as well as the last value... Only hdd converting shows correct value.
            //I have no idea how to set early for example value 2, because we don't know what time zone is in CreationTime.
            if ($PercentComplete < 1 || $PercentComplete > 80){ //TODO: SolidCP 1.4.8 has bugged indicator, just don't show 100, until not get ProvisioningStatus OK
                $PercentComplete = 80;
            }
            $provisioningStatus['task'] = [
                'PercentComplete' => $PercentComplete,
                'CreationTime' => $result['CreationTime'],
                'Status' => $taskResult['Status'],
            ];
        }

        if(!empty($result['CurrentTaskId']) && $result['ProvisioningStatus'] === 'Error'){ //can get ERROR before OK status (that how works solidcp)
            $provisioningStatus['ProvisioningStatus'] = 'InProgress';
            $provisioningStatus['task'] = [
                'PercentComplete' => 95,
                'CreationTime' => $result['CreationTime'],
                'Status' => 'Run',
            ];
        }

        if(empty($result['CurrentTaskId']) && $result['ProvisioningStatus'] === 'OK'){
            $provisioningStatus['task'] = [
                'PercentComplete' => 100,
                'CreationTime' => $result['CreationTime'],
                'Status' => 'Complete',
            ];
        }

        return $provisioningStatus;
    }
}