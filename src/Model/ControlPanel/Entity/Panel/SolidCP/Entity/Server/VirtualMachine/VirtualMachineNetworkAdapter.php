<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine;

final class VirtualMachineNetworkAdapter
{
    public string $name;
    public array $iPAddresses;
    public string $macAddress;
    public string $smwitchName;
    public int $vlan;
}