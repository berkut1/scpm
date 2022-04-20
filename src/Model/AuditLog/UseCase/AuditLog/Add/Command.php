<?php
declare(strict_types=1);

namespace App\Model\AuditLog\UseCase\AuditLog\Add;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\Entity\TaskNameInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public Entity $entity;

    #[Assert\NotBlank]
    public TaskNameInterface $taskName;

    /** @var Record[] */
    #[Assert\NotBlank]
    public array $records;

    /**
     * @param Entity $entity
     * @param TaskNameInterface $taskName
     * @param Record[] $records
     */
    public function __construct(Entity $entity, TaskNameInterface $taskName, array $records = [])
    {
        $this->entity = $entity;
        $this->taskName = $taskName;
        $this->records = $records;
    }
}