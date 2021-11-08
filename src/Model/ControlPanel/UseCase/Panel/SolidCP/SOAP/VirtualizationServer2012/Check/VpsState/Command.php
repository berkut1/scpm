<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsState;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher = null;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $solidcp_item_id = 0;


    public function __construct(int $solidcp_item_id, ?int $id_enterprise_dispatcher = null )
    {
        $this->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $this->solidcp_item_id = $solidcp_item_id;
    }
}