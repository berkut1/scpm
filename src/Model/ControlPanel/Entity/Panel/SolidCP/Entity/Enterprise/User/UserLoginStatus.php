<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User;

use Webmozart\Assert\Assert;

final class UserLoginStatus implements \Stringable
{
    public const string ENABLED = 'Enabled';
    public const string DISABLED = 'Disabled';
    public const string LOCKEDOUT = 'Lockedout';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::ENABLED,
            self::DISABLED,
            self::LOCKEDOUT,

        ]);
        $this->name = $name;
    }

    public static function list(): array
    {
        return [
            0 => self::ENABLED,
            1 => self::DISABLED,
            2 => self::LOCKEDOUT,
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

    public static function default(): self
    {
        return new self(self::ENABLED);
    }

    public static function enabled(): self
    {
        return new self(self::ENABLED);
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