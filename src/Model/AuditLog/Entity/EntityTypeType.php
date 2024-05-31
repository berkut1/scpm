<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class EntityTypeType extends StringType
{
    final public const string NAME = 'audit_log_entity_type';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof EntityTypeInterface ? $value->getName() : $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EntityTypeInterface
    {
        return !empty($value) ? EntityType::create($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}