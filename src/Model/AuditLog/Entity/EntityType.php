<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Webmozart\Assert\Assert;

final class EntityType implements EntityTypeInterface
{
    public const string ENTITY_AUDIT_LOG = 'audit_log';
    private string $name;

    #[\Override]
    public static function create(string $name): self
    {
        Assert::oneOf($name, [
            self::ENTITY_AUDIT_LOG,
        ]);
        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    public static function auditLog(): self
    {
        return self::create(self::ENTITY_AUDIT_LOG);
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