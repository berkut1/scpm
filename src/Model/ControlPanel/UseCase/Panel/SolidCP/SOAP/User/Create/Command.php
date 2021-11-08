<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher = null;
    /**
     * @Assert\NotBlank()
     */
    public string $username = '';
    public ?string $firstName = null;
    public ?string $lastName = null;
    /**
     * @Assert\NotBlank()
     */
    public string $email = '';
    /**
     * @Assert\NotBlank()
     */
    public string $password = '';

    #[Pure]
    public static function create(string $username, ?string $firstName, ?string $lastName, string $email, string $password, ?int $id_enterprise_dispatcher = null): self
    {
        $command = new self();
        $command->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $command->username = $username;
        $command->firstName = $firstName;
        $command->lastName = $lastName;
        $command->email = $email;
        $command->password = $password;
        return $command;
    }

}