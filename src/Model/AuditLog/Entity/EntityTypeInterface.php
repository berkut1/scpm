<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

interface EntityTypeInterface
{
    public static function create(string $name): self;
    public function isEqual(self $type): bool;
    public function getName(): string;
}