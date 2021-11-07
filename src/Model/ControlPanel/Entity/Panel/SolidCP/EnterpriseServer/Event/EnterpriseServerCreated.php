<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;

class EnterpriseServerCreated
{
    public EnterpriseServer $enterpriseServer;

    public function __construct(EnterpriseServer $enterpriseServer)
    {
        $this->enterpriseServer = $enterpriseServer;
    }
}