<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package;

use Webmozart\Assert\Assert;

class PackageType
{
    //must have the same name as class name
    public const PACKAGE_VIRTUAL_MACHINE = 'VirtualMachinePackage';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::PACKAGE_VIRTUAL_MACHINE,
        ]);

        $this->name = $name;
    }

    public static function getArray(): array
    {
        return [
            self::PACKAGE_VIRTUAL_MACHINE => self::PACKAGE_VIRTUAL_MACHINE,
        ];
    }

    public static function virtualMachine(): self
    {
        return new self(self::PACKAGE_VIRTUAL_MACHINE);
    }

    public function isEqual(self $type): bool
    {
        return $this->getName() === $type->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}