<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

final class Status implements \Stringable
{
    public const string STATUS_SUSPENDED = 'suspended';
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_ARCHIVED = 'archived';

    private string $name;

    public function __construct(string $name)
    {
        $name = strtolower($name);
        Assert::oneOf($name, [
            self::STATUS_ACTIVE,
            self::STATUS_SUSPENDED,
            self::STATUS_ARCHIVED,
        ]);

        $this->name = $name;
    }

    public static function getArray(): array
    {
        return [
            self::STATUS_ACTIVE => 'ACTIVE',
            self::STATUS_SUSPENDED => 'SUSPENDED',
        ];
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function suspended(): self
    {
        return new self(self::STATUS_SUSPENDED);
    }

    public static function archived(): self
    {
        return new self(self::STATUS_ARCHIVED);
    }

    public function isActive(): bool
    {
        return $this->name === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->name === self::STATUS_SUSPENDED;
    }

    public function isArchived(): bool
    {
        return $this->name === self::STATUS_ARCHIVED;
    }

    public function isEqual(self $status): bool
    {
        return $this->getName() === $status->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getName();
    }
}