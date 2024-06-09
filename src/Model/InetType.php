<?php
declare(strict_types=1);

namespace App\Model;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class InetType extends Type
{
    final public const string NAME = 'inet';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'INET';
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}