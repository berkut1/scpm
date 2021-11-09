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
        'provisioning_status' => "string",
        'task' => "array",
    ])]
    public function handle(Command $command): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $virtualMachineItem = $esVirtualizationServer2012->getVirtualMachineItem($command->solidcp_item_id);
        $provisioningStatus = $virtualMachineItem['ProvisioningStatus'];
        $percentComplete = 0;
        $creationTime = $virtualMachineItem['CreationTime'];
        $Status = null;

        if(!empty($virtualMachineItem['CurrentTaskId'])){
            $esTask = EsTasks::createFromEnterpriseDispatcher($enterpriseDispatcher);
            $taskResult = $esTask->getTask($virtualMachineItem['CurrentTaskId']);
            $percentComplete = $taskResult['IndicatorCurrent']; //the earliest value can be -1 or 0, as well as the last value... Only hdd converting shows correct value.
            //I have no idea how to set early for example value 2, because we don't know what time zone is in CreationTime.
            if ($percentComplete < 1 || $percentComplete > 80){ //TODO: SolidCP 1.4.8 has bugged indicator, just don't show 100, until not get ProvisioningStatus OK
                $percentComplete = 80;
            }
            $Status = $taskResult['Status'];
        }

        if(!empty($virtualMachineItem['CurrentTaskId']) && $provisioningStatus === 'Error'){ //can get ERROR before OK status (that how works solidcp)
            $provisioningStatus = 'InProgress';
            $percentComplete = 95;
            $Status = 'Run';
        }

        if(empty($virtualMachineItem['CurrentTaskId']) && $provisioningStatus === 'OK'){
            $percentComplete = 100;
            $Status = 'Complete';
        }

        return [
            'provisioning_status' => $provisioningStatus,
            'task' => [
                'percent_complete' => $percentComplete,
                'creation_time' => $creationTime,
                'status' => $Status,
            ],
        ];
    }
}