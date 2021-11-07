<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

class EntityType implements EntityTypeInterface
{
    private string $name;

    public static function create(string $name): EntityTypeInterface
    {
        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    public function isEqual(EntityTypeInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}