<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class Id extends Uuid implements \Stringable
{
    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $uid = Uuid::fromString($value)->uid;
        parent::__construct($uid);
    }

    public static function zeros(): self
    {
        return new self(Uuid::NIL);
    }

    public static function next(): self
    {
        return new self(self::v7()::generate());
    }

    public function isEqual(self $id): bool
    {
        return $this->equals($id);
    }

    public function getValue(): string
    {
        return $this->toRfc4122();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getValue();
    }
}
