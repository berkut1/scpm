<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $name = '';
    /**
     * @Assert\NotBlank()
     */
    public string $url = '';
    /**
     * @Assert\NotBlank()
     */
    public string $login = '';
    /**
     * @Assert\NotBlank()
     */
    public string $password = '';
}