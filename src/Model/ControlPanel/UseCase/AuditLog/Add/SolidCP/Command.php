<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\AuditLog\Add\SolidCP;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\Entity\TaskNameInterface;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public EnterpriseServer $enterpriseServer;
    /**
     * @Assert\NotBlank()
     */
    public Entity $entity;
    /**
     * @Assert\NotBlank()
     */
    public TaskNameInterface $taskName;
    /**
     * @Assert\NotBlank()
     */
    public array $records;

    /** When we call this method, we always have EnterpriseServer, because we do SOAP call to SolidCP
     * @param EnterpriseServer $enterpriseServer
     * @param Entity $entity
     * @param TaskNameInterface $taskName
     * @param Record[] $records
     */
    public function __construct(EnterpriseServer $enterpriseServer, Entity $entity, TaskNameInterface $taskName, array $records = [])
    {
        $this->enterpriseServer = $enterpriseServer;
        $this->entity = $entity;
        $this->taskName = $taskName;
        $this->records = $records;
    }
}