<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Edit\Password;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher;
    /**
     * @Assert\NotBlank()
     */
    public string $username;
    /**
     * @Assert\NotBlank()
     */
    public string $new_password;

}
