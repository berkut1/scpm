<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine;

use Webmozart\Assert\Assert;

class VirtualMachineProvisioningStatus
{
    const UNKNOWN = 'Unknown';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::UNKNOWN,
            'OK',
            'Warning',
            'Error',
            'InProgress',
            'DeletionProgress',
            'Deleted',
        ]);

        $this->name = $name;
    }

    public static function default(): self
    {
        return new self(self::UNKNOWN);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}