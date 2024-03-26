<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public int $id = 0;

    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotBlank]
    public string $url = '';

    #[Assert\NotBlank]
    public string $login = '';

    #[Assert\NotBlank]
    public string $password = '';

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self
    {
        $command = new self($enterpriseDispatcher->getId());
        $command->name = $enterpriseDispatcher->getName();
        $command->url = $enterpriseDispatcher->getUrl();
        $command->login = $enterpriseDispatcher->getLogin();
        $command->password = $enterpriseDispatcher->getPassword();
        return $command;
    }
}