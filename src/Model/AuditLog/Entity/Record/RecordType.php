<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity\Record;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class RecordType extends JsonType
{
    public const NAME = 'audit_log_record_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof ArrayCollection) {
            $data = $value->toArray();
        } else {
            $data = $value;
        }

        return parent::convertToDatabaseValue($data, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (!is_array($data = parent::convertToPHPValue($value, $platform))) {
            return $data;
        }
        return new ArrayCollection(array_map([self::class, 'serialize'], $data)); //serialize to attribute class
    }

    private static function serialize(array $data): Record
    {
        return Record::setFromDecodedJSON($data);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}