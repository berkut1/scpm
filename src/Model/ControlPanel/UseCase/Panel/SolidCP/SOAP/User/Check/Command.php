<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public ?int $id_enterprise_dispatcher = null;

    #[Assert\NotBlank]
    public string $username = '';

    public static function create(string $username, ?int $id_enterprise_dispatcher = null): self
    {
        $command = new self();
        $command->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $command->username = $username;
        return $command;
    }
}