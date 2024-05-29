<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsState\ByIP;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public ?int $id_enterprise_dispatcher = null;

    #[Assert\NotBlank]
    public string $vps_ip_address = '';
}
