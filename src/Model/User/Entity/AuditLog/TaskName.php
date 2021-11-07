<?php
declare(strict_types=1);

namespace App\Model\User\Entity\AuditLog;

use App\Model\AuditLog\Entity\TaskNameInterface;
use Webmozart\Assert\Assert;

class TaskName implements TaskNameInterface
{
    public const TASK_CREATE_USER = 'create_user';
    public const TASK_LOGIN_USER = 'login_user';
    private string $name;

    public static function create(string $name): self
    {
        Assert::oneOf($name, [
            self::TASK_CREATE_USER,
            self::TASK_LOGIN_USER,
        ]);

        $entityType = new self();
        $entityType->name = $name;
        return $entityType;
    }

    public static function createUser(): self
    {
        return self::create(self::TASK_CREATE_USER);
    }

    public static function loginUser(): self
    {
        return self::create(self::TASK_LOGIN_USER);
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