<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class Id implements \Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid7()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getValue();
    }
}
