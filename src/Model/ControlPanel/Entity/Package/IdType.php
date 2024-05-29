<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class IdType extends GuidType
{
    public const string NAME = 'cp_package_id';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return !empty($value) ? new Id($value) : null;
    }

    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }
}
