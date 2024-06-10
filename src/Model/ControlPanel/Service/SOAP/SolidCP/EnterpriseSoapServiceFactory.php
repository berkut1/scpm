<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SOAP\BaseSoapExecute;

abstract class EnterpriseSoapServiceFactory extends BaseSoapExecute
{
    protected const string SERVICE = '';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher, $caching = false, $compression = true
    ): static
    {
        $soap = new static();
        $soap->initManual($enterpriseDispatcher->getUrl(), $enterpriseDispatcher->getLogin(), $enterpriseDispatcher->getPassword(), $caching, $compression);
        return $soap;
    }

    #[\Override]
    protected function getServiceWsdl(): string
    {
        return $this->url . '/' . static::SERVICE . '?WSDL';
    }
}