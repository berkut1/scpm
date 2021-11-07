<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine;

use Webmozart\Assert\Assert;

class ReplicationState
{
    const DISABLED = 'Disabled';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::DISABLED,
            'Error',
            'FailOverWaitingCompletion',
            'FailedOver',
            'NotApplicable',
            'ReadyForInitialReplication',
            'InitialReplicationInProgress',
            'Replicating',
            'Resynchronizing',
            'ResynchronizeSuspended',
            'Suspended',
            'SyncedReplicationComplete',
            'WaitingForInitialReplication',
            'WaitingForStartResynchronize',
        ]);

        $this->name = $name;
    }

    public static function default(): self
    {
        return new self(self::DISABLED);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}