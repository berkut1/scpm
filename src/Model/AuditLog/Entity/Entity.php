<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class Entity
{
    #[ORM\Column(type: "audit_log_entity_type", length: 255, nullable: false)]
    private EntityTypeInterface $type;

    /** We use string because not only UUID is possible to be here */
    #[ORM\Column(type: Types::STRING, length: 36, nullable: false)]
    private string $id;

    public function __construct(EntityTypeInterface $type, string $id)
    {
        Assert::notEmpty($type);
        Assert::notEmpty($id);

        $this->type = $type;
        $this->id = $id;
    }

    public function getType(): EntityTypeInterface
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
