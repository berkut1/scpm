<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EntityTypeType extends StringType
{
    public const NAME = 'audit_log_entity_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof EntityTypeInterface ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EntityTypeInterface
    {
        return !empty($value) ? EntityType::create($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}