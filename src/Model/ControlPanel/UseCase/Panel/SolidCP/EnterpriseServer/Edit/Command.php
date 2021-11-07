<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Edit;

use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     */
    public string $name;
    /**
     * @Assert\NotBlank()
     */
    public string $url;
    /**
     * @Assert\NotBlank()
     */
    public string $login;
    /**
     * @Assert\NotBlank()
     */
    public string $password;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    #[Pure]
    public static function fromEnterpriseServer(EnterpriseServer $enterpriseServer): self
    {
        $command = new self($enterpriseServer->getId());
        $command->name = $enterpriseServer->getName();
        $command->url = $enterpriseServer->getUrl();
        $command->login = $enterpriseServer->getLogin();
        $command->password = $enterpriseServer->getPassword();
        return $command;
    }
}