<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine;

use Webmozart\Assert\Assert;

class OperationalStatus
{
    const NONE = 'None';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::NONE,
            'Ok',
            'Error',
            'NoContact',
            'LostCommunication',
            'Paused',
        ]);

        $this->name = $name;
    }

    public static function default(): self
    {
        return new self(self::NONE);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}