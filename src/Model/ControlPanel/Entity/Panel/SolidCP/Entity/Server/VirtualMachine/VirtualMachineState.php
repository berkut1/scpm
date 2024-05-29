<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine;

use Webmozart\Assert\Assert;

final class VirtualMachineState implements \Stringable
{
    public const string UNKNOWN = 'Unknown';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::UNKNOWN,
            'Other',
            'Running',
            'Off',
            'Stopping',
            'Saved',
            'Paused',
            'Starting',
            'Reset',
            'Saving',
            'Pausing',
            'Resuming',
            'FastSaved',
            'FastSaving',
            'RunningCritical',
            'OffCritical',
            'StoppingCritical',
            'SavedCritical',
            'PausedCritical',
            'StartingCritical',
            'ResetCritical',
            'SavingCritical',
            'PausingCritical',
            'ResumingCritical',
            'FastSavedCritical',
            'FastSavingCritical',
            'Snapshotting',
            'Migrating',
            'Deleted',

        ]);
        $this->name = $name;
    }

    public static function default(): self
    {
        return new self(self::UNKNOWN);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }

}