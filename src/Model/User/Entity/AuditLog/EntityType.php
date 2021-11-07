<?php
declare(strict_types=1);

namespace App\Model\User\Entity\AuditLog;

use App\Model\AuditLog\Entity\EntityTypeInterface;
use Webmozart\Assert\Assert;

class EntityType implements EntityTypeInterface
{
    public const ENTITY_USER_USER = 'user_user';
    private string $name;

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

    public function isEqual(EntityTypeInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}