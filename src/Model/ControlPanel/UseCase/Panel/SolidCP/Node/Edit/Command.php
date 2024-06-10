<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Edit;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    public ?int $id;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_enterprise_dispatcher = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_location = null;

    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $cores = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $threads = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $ram_mb = null;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromSolidcpServer(SolidcpServer $solidcpServer): self
    {
        $command = new self($solidcpServer->getId());
        $command->id_enterprise_dispatcher = $solidcpServer->getEnterprise()->getId();
        $command->id_location = $solidcpServer->getLocation()->getId();
        $command->name = $solidcpServer->getName();
        $command->cores = $solidcpServer->getCores();
        $command->threads = $solidcpServer->getThreads();
        $command->ram_mb = $solidcpServer->getMemoryMb();
        return $command;
    }
}