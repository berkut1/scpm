<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\ChangeNode;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    private readonly int $id_hosting_space;

    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $id_enterprise_dispatcher = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $id_server = 0;

    private function __construct(int $id_hosting_space)
    {
        $this->id_hosting_space = $id_hosting_space;
    }

    public static function fromHostingSpace(SolidcpHostingSpace $solidcpHostingSpace): self
    {
        $command = new self($solidcpHostingSpace->getId());
        $command->id_enterprise_dispatcher = $solidcpHostingSpace->getSolidcpServer()->getEnterprise()->getId();
        $command->id_server = $solidcpHostingSpace->getSolidcpServer()->getId();
        return $command;
    }

    public function getIdHostingSpace(): int
    {
        return $this->id_hosting_space;
    }

    public function getIdEnterpriseDispatcher(): int
    {
        return $this->id_enterprise_dispatcher;
    }
}