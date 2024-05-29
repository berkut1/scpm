<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class PackageTypeType extends StringType
{
    public const string NAME = 'cp_package_type';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof PackageType ? $value->getName() : $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?PackageType
    {
        return !empty($value) ? new PackageType($value) : null;
    }

    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }
}