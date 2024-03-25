<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Webmozart\Assert\Assert;

final class UserId
{
    final public const string SYSTEM_USER_ID = '00000000-0000-0000-0000-000000000001';
    final public const string JWT_USER_ID = '00000000-0000-0000-0000-000000000002'; //https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/8-jwt-user-provider.md
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function systemUserId(): self
    {
        return new self(self::SYSTEM_USER_ID);
    }

    public static function jwtUserId(): self
    {
        return new self(self::JWT_USER_ID);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqualTo(self $id): bool
    {
        return $this->getValue() === $id->getValue();
    }
}