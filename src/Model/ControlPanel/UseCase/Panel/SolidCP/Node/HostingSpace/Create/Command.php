<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\Create;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $name = '';
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    private int $id_enterprise;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    private int $id_server;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $id_hosting_space = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $max_active_number = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $max_reserved_memory_mb = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $space_quota_gb = 0;

    private function __construct(int $id_server)
    {
        $this->id_server = $id_server;
    }

    public static function fromServer(SolidcpServer $solidcpServer): self
    {
        $command = new self($solidcpServer->getId());
        $command->id_enterprise = $solidcpServer->getEnterprise()->getId();
        return $command;
    }

    public function getIdEnterprise(): int
    {
        return $this->id_enterprise;
    }

    public function getIdServer(): int
    {
        return $this->id_server;
    }



}