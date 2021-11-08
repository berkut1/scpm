<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\ChangeStatus;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\Package\PackageStatus;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
    }

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $esPackages = EsPackages::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $esPackages->changePackageStatus($command->solidcp_package_id, new PackageStatus($command->solidcp_package_status));
    }
}
