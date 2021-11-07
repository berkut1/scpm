<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsProvisioningStatus;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise = null;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $solidcp_item_id = 0;


    public function __construct(int $solidcp_item_id, ?int $id_enterprise = null )
    {
        $this->id_enterprise = $id_enterprise;
        $this->solidcp_item_id = $solidcp_item_id;
    }
}