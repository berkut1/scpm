<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\Package;

use Webmozart\Assert\Assert;

class PackageStatus
{
    const ACTIVE = 'Active';
    const SUSPENDED = 'Suspended';
    const CANCELLED = 'Cancelled';
    private string $name;

    public function __construct(string $name)
    {
        $name = ucfirst(strtolower($name)); //make sure we have correct status letter case
        Assert::oneOf($name, [
            self::ACTIVE,
            self::SUSPENDED,
            self::CANCELLED,

        ]);
        $this->name = $name;
    }

    public static function list(): array
    {
        return [
            1 => self::ACTIVE,
            2 => self::SUSPENDED,
            3 => self::CANCELLED,
        ];
    }

    /**
     * @throws \Exception
     */
    public function getId(): int
    {
        foreach (self::list() as $key => $value){
            if($this->name === $value){
                return $key;
            }
        }
        throw new \Exception('Not Found Id');
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function suspended(): self
    {
        return new self(self::SUSPENDED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}