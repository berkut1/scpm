<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeStatus;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise;
    /**
     * @Assert\NotBlank()
     */
    public string $vps_ip_address;
    /**
     * @Assert\NotBlank()
     */
    public string $vps_status;

    public static function create(string $vps_ip_address, string $vps_status, ?int $id_enterprise = null): self
    {
        $command = new self();
        $command->id_enterprise = $id_enterprise;
        $command->vps_status = $vps_status;
        $command->vps_ip_address = $vps_ip_address;
        return $command;
    }
}
