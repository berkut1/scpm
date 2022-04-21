<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeStatus;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher;

    #[Assert\NotBlank]
    public string $client_login;

    #[Assert\NotBlank]
    public string $vps_ip_address;

    #[Assert\NotBlank]
    public string $vps_status;

    public static function create(string $client_login, $vps_ip_address, string $vps_status, ?int $id_enterprise_dispatcher = null): self
    {
        $command = new self();
        $command->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $command->client_login = $client_login;
        $command->vps_status = $vps_status;
        $command->vps_ip_address = $vps_ip_address;
        return $command;
    }
}
