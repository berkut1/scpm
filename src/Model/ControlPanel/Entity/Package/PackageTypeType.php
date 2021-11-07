<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class PackageTypeType extends StringType
{
    public const NAME = 'cp_package_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof PackageType ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PackageType
    {
        return !empty($value) ? new PackageType($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}