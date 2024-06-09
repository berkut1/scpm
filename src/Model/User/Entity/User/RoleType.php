<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class RoleType extends StringType
{
    public const string NAME = 'user_user_role';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof Role ? $value->getName() : $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Role
    {
        return !empty($value) ? new Role($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
