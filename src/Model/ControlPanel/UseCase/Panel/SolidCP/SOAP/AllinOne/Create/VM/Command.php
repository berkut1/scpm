<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\AllinOne\Create\VM;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher = null;

    #[Assert\NotBlank]
    public string $client_login = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $client_email = '';

    #[Assert\NotBlank]
    public string $client_password = '';

    #[Assert\NotBlank]
    public string $server_package_name = '';

    #[Assert\NotBlank]
    public string $server_location_name = '';

    #[Assert\NotBlank]
    public string $server_os_name = '';

    #[Assert\NotBlank]
    public string $server_password = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $server_ip_amount = 0;
    public array $ignore_node_ids = [];
    public array $ignore_hosting_space_ids = [];
}