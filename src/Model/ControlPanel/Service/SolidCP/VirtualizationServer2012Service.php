<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsVirtualizationServer2012;

final readonly class VirtualizationServer2012Service
{
    public function __construct(private EnterpriseDispatcherRepository $enterpriseDispatcherRepository) {}

    /**
     * @throws \SoapFault
     */
    public function allOsTemplateListFrom(int $id_enterprise_dispatcher, int $packageId): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($id_enterprise_dispatcher);
        $esVirtualizationServer2012 = EsVirtualizationServer2012::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $libraryItem = $esVirtualizationServer2012->getOperatingSystemTemplates($packageId)['LibraryItem'];
        $templates = [];
        foreach ($libraryItem as $item) {
            $templates[$item['Path']] = $item['Name'];
        }
        return $templates;
    }
}