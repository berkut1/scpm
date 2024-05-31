<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity\Record;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

final class RecordType extends JsonType
{
    final public const string NAME = 'audit_log_record_type';

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof ArrayCollection) {
            $data = $value->toArray();
        } else {
            $data = $value;
        }

        return parent::convertToDatabaseValue($data, $platform);
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?ArrayCollection
    {
        if (!is_array($data = parent::convertToPHPValue($value, $platform))) {
            return $data;
        }
        return new ArrayCollection(array_map(self::serialize(...), $data)); //serialize to attribute class
    }

    private static function serialize(array $data): Record
    {
        return Record::setFromDecodedJSON($data);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}