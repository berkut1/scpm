<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Webmozart\Assert\Assert;

final class TaskName implements TaskNameInterface
{
    public const string TASK_REMOVE_LOG = 'remove_log';
    private string $name;

    #[\Override]
    public static function create(string $name): self
    {
        Assert::oneOf($name, [
            self::TASK_REMOVE_LOG,
        ]);
        $taskName = new self();
        $taskName->name = $name;
        return $taskName;
    }

    public static function removeLog(): self
    {
        return self::create(self::TASK_REMOVE_LOG);
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