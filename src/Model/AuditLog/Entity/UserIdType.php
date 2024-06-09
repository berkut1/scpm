<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class UserIdType extends GuidType
{
    final public const string NAME = 'audit_log_user_id';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof UserId ? $value->getValue() : $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        return !empty($value) ? new UserId($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}