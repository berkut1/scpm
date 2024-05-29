<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

final class Role implements \Stringable
{
    public const string USER = 'ROLE_USER';
    public const string ADMIN = 'ROLE_ADMIN';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::USER,
            self::ADMIN,
        ]);

        $this->name = $name;
    }

    public static function getArray(): array
    {
        return [
            self::USER => self::USER,
            self::ADMIN => self::ADMIN,
        ];
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public static function default(): self
    {
        return new self(self::USER);
    }

    public function isUser(): bool
    {
        return $this->name === self::USER;
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    public function isEqual(self $role): bool
    {
        return $this->getName() === $role->getName();
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
