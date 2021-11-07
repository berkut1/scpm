<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TaskNameType extends StringType
{
    public const NAME = 'audit_log_task_name_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof TaskNameInterface ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?TaskNameInterface
    {
        return !empty($value) ? TaskName::create($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}