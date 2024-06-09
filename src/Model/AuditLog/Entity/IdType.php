<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class IdType extends GuidType
{
    final public const string NAME = 'audit_log_id';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return !empty($value) ? new Id($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
