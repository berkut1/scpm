<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

class TaskName implements TaskNameInterface
{
    private string $name;

    public static function create(string $name): TaskNameInterface
    {
        $taskName = new self();
        $taskName->name = $name;
        return $taskName;
    }

    public function isEqual(TaskNameInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}