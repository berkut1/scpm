<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise = null;
    /**
     * @Assert\NotBlank()
     */
    public string $username = '';

    public static function create(string $username, ?int $id_enterprise = null): self
    {
        $command = new self();
        $command->id_enterprise = $id_enterprise;
        $command->username = $username;
        return $command;
    }
}