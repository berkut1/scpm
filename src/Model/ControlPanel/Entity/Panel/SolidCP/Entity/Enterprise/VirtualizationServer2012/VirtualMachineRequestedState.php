<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\VirtualizationServer2012;

use Webmozart\Assert\Assert;

final class VirtualMachineRequestedState implements \Stringable
{
    public const string START = 'Start';
    public const string TURNOFF = 'TurnOff';
    public const string SHUTDOWN = 'ShutDown';
    public const string REBOOT = 'Reboot';
    public const string RESUME = 'Resume';
    public const string RESET = 'Reset';
    public const string PAUSE = 'Pause';
    public const string SAVE = 'Save';
    private string $name;

    public function __construct(string $name)
    {
        $name = $this->tryToGetCorrectCaseName($name); //make sure we have correct state letter case
        Assert::oneOf($name, $this->stringEnumArray());
        $this->name = $name;
    }

    public static function list(): array
    {
        return [
            2 => self::START,
            3 => self::TURNOFF,
            4 => self::SHUTDOWN,
            5 => self::REBOOT,
            6 => self::RESUME,
            10 => self::RESET,
            32768 => self::PAUSE,
            32769 => self::SAVE,
        ];
    }

    private function tryToGetCorrectCaseName(string $name): string
    {
        foreach ($this->stringEnumArray() as $value) {
            if (strtolower($name) === strtolower((string)$value)) {
                return $value;
            }
        }
        return $name; //we just try, if not found return the original value
    }

    private function stringEnumArray(): array
    {
        return [
            self::START,
            self::TURNOFF,
            self::SHUTDOWN,
            self::REBOOT,
            self::RESUME,
            self::RESET,
            self::PAUSE,
            self::SAVE,
        ];
    }

    /**
     * @throws \Exception
     */
    public function getId(): int
    {
        foreach (self::list() as $key => $value) {
            if ($this->name === $value) {
                return $key;
            }
        }
        throw new \Exception('Not Found Id');
    }

    public static function start(): self
    {
        return new self(self::START);
    }

    public static function turnoff(): self
    {
        return new self(self::TURNOFF);
    }

    public static function shutdown(): self
    {
        return new self(self::SHUTDOWN);
    }

    public static function reboot(): self
    {
        return new self(self::REBOOT);
    }

    public static function reset(): self
    {
        return new self(self::RESET);
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