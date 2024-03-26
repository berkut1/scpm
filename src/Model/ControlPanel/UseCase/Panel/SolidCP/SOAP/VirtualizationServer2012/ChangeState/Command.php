<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeState;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public ?int $id_enterprise_dispatcher = null;

    #[Assert\NotBlank]
    public string $client_login = '';

    #[Assert\NotBlank]
    public string $vps_ip_address = '';

    #[Assert\NotBlank]
    public string $vps_state = '';

    public static function create(string $client_login, $vps_ip_address, string $vps_state, ?int $id_enterprise_dispatcher = null): self
    {
        $command = new self();
        $command->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $command->client_login = $client_login;
        $command->vps_state = $vps_state;
        $command->vps_ip_address = $vps_ip_address;
        return $command;
    }
}
