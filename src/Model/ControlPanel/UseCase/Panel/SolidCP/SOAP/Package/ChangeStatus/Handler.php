<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\ChangeStatus;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\Package\PackageStatus;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;

class Handler
{
    private EnterpriseServerRepository $enterpriseServerRepository;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository)
    {
        $this->enterpriseServerRepository = $enterpriseServerRepository;
    }

    public function handle(Command $command): void
    {
        $enterpriseServer = $this->enterpriseServerRepository->getDefaultOrById($command->id_enterprise);

        $esPackages = EsPackages::createFromEnterpriseServer($enterpriseServer);
        $esPackages->changePackageStatus($command->solidcp_package_id, new PackageStatus($command->solidcp_package_status));
    }
}
