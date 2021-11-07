<?php
declare(strict_types=1);

namespace App\Model;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class BigIntType extends Type //we override that Type because of this https://github.com/doctrine/dbal/issues/3690
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?int
    {
        return $value === null ? null : (int) $value;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBigIntTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return Types::BIGINT;
    }
}