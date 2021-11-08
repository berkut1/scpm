<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;

class EnterpriseDispatcherCreated
{
    public EnterpriseDispatcher $enterpriseDispatcher;

    public function __construct(EnterpriseDispatcher $enterpriseDispatcher)
    {
        $this->enterpriseDispatcher = $enterpriseDispatcher;
    }
}