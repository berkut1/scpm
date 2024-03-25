<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

final class EntityType implements EntityTypeInterface
{
    private string $name;

    #[\Override]
    public static function create(string $name): EntityTypeInterface
    {
        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    #[\Override]
    public function isEqual(EntityTypeInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }
}