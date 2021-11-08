<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;
use Doctrine\DBAL\Connection;

class VirtualizationServer2012Service
{
    private Connection $connection;
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;

    public function __construct(Connection $connection, EnterpriseDispatcherRepository $enterpriseDispatcherRepository)
    {
        $this->connection = $connection;
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
    }

    public function allOsTemplateListFrom(int $id_enterprise_dispatcher, int $packageId): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($id_enterprise_dispatcher);
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $libraryItem = $esVirtualizationServer2012->getOperatingSystemTemplates($packageId)['LibraryItem'];
        $templates = [];
        foreach ($libraryItem as $item){
            $templates[$item['Path']] = $item['Name'];
        }
        return $templates;
    }
}