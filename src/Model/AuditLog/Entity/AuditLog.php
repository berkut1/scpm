<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use App\Model\AuditLog\Entity\Record\Record;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AuditLogs
 *
 * @ORM\Table(
 *     name="audit_logs",
 *     indexes={
 *          @ORM\Index(name="audit_logs_entity_type_id_idx", columns={"entity_type", "entity_id"})
 *  })
 * @ORM\Entity
 */
class AuditLog
{
    /**
     * @ORM\Column(name="id", type="audit_log_id", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private Id $id;

    /**
     * @ORM\Column(name="id_user", type="audit_log_user_id", nullable=false)
     */
    private UserId $idUser;

    /**
     * @ORM\Column(name="date", type="datetime_immutable", nullable=false, options={"default"="now()"})
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(name="ip_inet", type="string", nullable=false)
     */
    private string $ipInet;

    /**
     * @ORM\Embedded(class="Entity")
     */
    private Entity $entity;

    /**
     * @ORM\Column(name="task_name", type="audit_log_task_name_type", nullable=false)
     */
    private TaskNameInterface $taskName;

    /**
     * @var ArrayCollection|Record[]
     * @ORM\Column(name="records", type="audit_log_record_type", nullable=false, options={"jsonb"=true})
     */
    private array|ArrayCollection $records;

    private function __construct(Id $id, UserId $idUser, string $ip, Entity $entity, TaskNameInterface $taskName)
    {
        if(!inet_pton($ip)){
            throw new \DomainException("Wrong IP format for the ip $ip");
        }
        $this->id = $id;
        $this->date = new \DateTimeImmutable('now');
        $this->ipInet = $ip;
        $this->idUser = $idUser;
        $this->entity = $entity;
        $this->taskName = $taskName;
    }


    /**
     * @param Id $id
     * @param UserId $idUser
     * @param string $ip
     * @param Entity $entity
     * @param TaskNameInterface $taskName
     * @param Record[] $records
     * @return static
     */
    public static function create(Id $id, UserId $idUser, string $ip, Entity $entity, TaskNameInterface $taskName, array $records): self
    {
        $auditLog = new self($id, $idUser, $ip, $entity, $taskName);
        $auditLog->records = new ArrayCollection($records);
        return $auditLog;
    }

    public static function createAsSystem(Id $id, string $ip, Entity $entity, TaskNameInterface $taskName, array $records): self
    {
        $auditLog = new self($id, UserId::systemUserId(), $ip, $entity, $taskName);
        $auditLog->records = new ArrayCollection($records);
        return $auditLog;
    }

    public function withCustomTime(\DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->date = $date;
        return $clone;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getIdUser(): UserId
    {
        return $this->idUser;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function getTaskName(): TaskNameInterface
    {
        return $this->taskName;
    }

    /** @return Record[] */
    public function getRecords(): array
    {
        return $this->records->toArray();
    }
}
