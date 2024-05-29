<?php
declare(strict_types=1);

namespace App\Model\User\Entity\AuditLog;

use App\Model\AuditLog\Entity\EntityTypeInterface;
use Webmozart\Assert\Assert;

final class EntityType implements EntityTypeInterface
{
    public const string ENTITY_USER_USER = 'user_user';
    private string $name;

    #[\Override]
    public static function create(string $name): self
    {
        Assert::oneOf($name, [
            self::ENTITY_USER_USER,
        ]);

        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    public static function userUser(): self
    {
        return self::create(self::ENTITY_USER_USER);
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