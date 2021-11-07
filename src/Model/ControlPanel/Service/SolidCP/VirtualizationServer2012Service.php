<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;
use Doctrine\DBAL\Connection;

class VirtualizationServer2012Service
{
    private Connection $connection;
    private EnterpriseServerRepository $enterpriseServerRepository;

    public function __construct(Connection $connection, EnterpriseServerRepository $enterpriseServerRepository)
    {
        $this->connection = $connection;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
    }

    public function allOsTemplateListFrom(int $id_enterprise, int $packageId): array
    {
        $enterpriseServer = $this->enterpriseServerRepository->get($id_enterprise);
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseServer($enterpriseServer);
        $libraryItem = $esVirtualizationServer2012->getOperatingSystemTemplates($packageId)['LibraryItem'];
        $templates = [];
        foreach ($libraryItem as $item){
            $templates[$item['Path']] = $item['Name'];
        }
        return $templates;
    }
}