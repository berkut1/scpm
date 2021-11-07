<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User;

use Webmozart\Assert\Assert;

class UserRole
{
    const USER = 'User';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::USER,

        ]);
        $this->name = $name;
    }

    public static function list(): array
    {
        return [
            3 => self::USER,
        ];
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public function getId(): int
    {
        foreach (self::list() as $key => $value){
            if($this->name === $value){
                return $key;
            }
        }
        return -1;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}