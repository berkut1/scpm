<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

final class TaskName implements TaskNameInterface
{
    private string $name;

    #[\Override]
    public static function create(string $name): TaskNameInterface
    {
        $taskName = new self();
        $taskName->name = $name;
        return $taskName;
    }

    #[\Override]
    public function isEqual(TaskNameInterface $type): bool
    {
        return $this->getName() === $type->getName();
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }
}