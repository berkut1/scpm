<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    const ZEROS = "00000000-0000-0000-0000-000000000000";
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function zeros(): self
    {
        return new self(self::ZEROS);
    }

    public static function next(): self
    {
        return new self(Uuid::uuid6()->toString());
    }

    public function isEqual(self $id): bool
    {
        return $this->value === $id->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    #[Pure]
    public function __toString(): string
    {
        return $this->getValue();
    }
}
