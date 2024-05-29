<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeSolidCpHostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?int $id;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_enterprise_dispatcher = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_solidcp_hosting_space = 0;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromHostingSpace(SolidcpHostingSpace $solidcpHostingSpace): self
    {
        $command = new self($solidcpHostingSpace->getId());
        $command->id_enterprise_dispatcher = $solidcpHostingSpace->getSolidcpServer()->getEnterprise()->getId();
        $command->id_solidcp_hosting_space = $solidcpHostingSpace->getSolidCpIdHostingSpace();
        return $command;
    }
}