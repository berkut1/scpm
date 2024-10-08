<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User;

use Webmozart\Assert\Assert;

final class UserStatus implements \Stringable
{
    public const string ACTIVE = 'Active';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::ACTIVE,

        ]);
        $this->name = $name;
    }

    public static function list(): array
    {
        return [
            1 => self::ACTIVE,
        ];
    }

    public function getId(): int
    {
        foreach (self::list() as $key => $value) {
            if ($this->name === $value) {
                return $key;
            }
        }
        return -1;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }
}