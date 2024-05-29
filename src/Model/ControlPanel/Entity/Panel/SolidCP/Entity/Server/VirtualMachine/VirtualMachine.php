<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\ServiceProviderItem;

final class VirtualMachine extends ServiceProviderItem
{
    //packageId in ServiceProviderItem
    public int $CpuCores;
    public int $RamSize;
    /** @var int[] */
    public array $HddSize;
    public int $HddMaximumIOPS;
    public int $HddMinimumIOPS;
    public int $SnapshotsNumber;
    public bool $DvdDriveInstalled;
    public bool $BootFromCD;
    public bool $NumLockEnabled;
    public bool $StartTurnOffAllowed;
    public bool $PauseResumeAllowed;
    public bool $RebootAllowed;
    public bool $ResetAllowed;
    public bool $ReinstallAllowed;
    public bool $ExternalNetworkEnabled = false;
    public ?string $ExternalNicMacAddress = null;
    public bool $PrivateNetworkEnabled;
    public int $defaultaccessvlan;

    //default
    public VirtualMachineState $State;
    public OperationalStatus $Heartbeat;
    public VirtualMachineProvisioningStatus $ProvisioningStatus;
    public ReplicationState $ReplicationState;

    //optional
    public int $PrivateNetworkVlan = 0;
    public ?string $CustomPrivateGateway = null;
    public ?string $CustomPrivateDNS1 = null;
    public ?string $CustomPrivateDNS2 = null;
    public ?string $CustomPrivateMask = null;


    //autofill via service
    public ?string $hostname = null;
    public int $Uptime = 0; //int64
    public int $CpuUsage = 0;
    public int $RamUsage = 0;
    public bool $LegacyNetworkAdapter = false;
    public bool $RemoteDesktopEnabled = false;
    public bool $ManagementNetworkEnabled = false;
    public int $Generation = 0;
    public bool $EnableSecureBoot = false;
    public int $ProcessorCount = 0;
    public bool $NeedReboot = false;
    public ?string $ExternalSwitchId = null;
    public ?string $PrivateNicMacAddress = null;
    public ?string $PrivateSwitchId = null;
    public ?string $ManagementNicMacAddress = null;
    public ?string $ManagementSwitchId = null;
    /** @var null|VirtualMachineNetworkAdapter[] */
    public ?array $Adapters = null;
    /** @var null|VirtualHardDiskInfo[] */
    public ?array $Disks = null; //[];
    public ?string $Status = null;
    public ?string $SecureBootTemplate = null;
    public ?string $ParentSnapshotId = null;
    public ?VirtualMachineIPAddress $PrimaryIP = null;
    public ?string $ClusterName = null;
    public ?string $VirtualMachineId = null;
    public ?string $Domain = null;
    public ?string $Version = null;
    public ?string $CreationTime = null;
    public ?string $RootFolderPath = null;
    public ?string $VirtualHardDrivePath = null;
    public ?string $OperatingSystemTemplate = null;
    public ?string $OperatingSystemTemplatePath = null;
    public ?string $OperatingSystemTemplateDeployParams = null;
    public ?string $AdministratorPassword = null;
    public ?string $CurrentTaskId = null;
    public ?DynamicMemory $DynamicMemory = null;
    /** @var null|LogicalDisk[] */
    public ?array $HddLogicalDisks = null;
    public bool $IsClustered = false;

    public static function create(
        int     $PackageId,
        int     $CpuCores,
        int     $RamSize,
        array   $HddSize,//int $HddSize,
        int     $HddMinimumIOPS = 0,
        int     $HddMaximumIOPS = 0,
        int     $SnapshotsNumber = 0,
        ?string $Hostname = null,
        bool    $DvdDriveInstalled = true,
        bool    $bootFromCD = false,
        bool    $numLockEnabled = false,
        bool    $StartTurnOffAllowed = true,
        bool    $PauseResumeAllowed = true,
        bool    $RebootAllowed = true,
        bool    $ResetAllowed = true,
        bool    $ReinstallAllowed = false,
        bool    $ExternalNetworkEnabled = true,
        ?string $ExternalNicMacAddress = null,
        bool    $PrivateNetworkEnabled = false,
        int     $Defaultaccessvlan = 0
    ): self
    {
        $vm = new self();
        $vm->PackageId = $PackageId;
        $vm->Name = $Hostname;
        $vm->CpuCores = $CpuCores;
        $vm->RamSize = $RamSize;
        $vm->HddSize = $HddSize;
        $vm->HddMaximumIOPS = $HddMaximumIOPS;
        $vm->HddMinimumIOPS = $HddMinimumIOPS;
        $vm->SnapshotsNumber = $SnapshotsNumber;
        $vm->DvdDriveInstalled = $DvdDriveInstalled;
        $vm->BootFromCD = $bootFromCD;
        $vm->NumLockEnabled = $numLockEnabled;
        $vm->StartTurnOffAllowed = $StartTurnOffAllowed;
        $vm->PauseResumeAllowed = $PauseResumeAllowed;
        $vm->RebootAllowed = $RebootAllowed;
        $vm->ResetAllowed = $ResetAllowed;
        $vm->ReinstallAllowed = $ReinstallAllowed;
        $vm->ExternalNetworkEnabled = $ExternalNetworkEnabled;
        $vm->ExternalNicMacAddress = $ExternalNicMacAddress;
        $vm->PrivateNetworkEnabled = $PrivateNetworkEnabled;
        $vm->defaultaccessvlan = $Defaultaccessvlan;
        $vm->State = VirtualMachineState::default();
        $vm->Heartbeat = OperationalStatus::default();
        $vm->ReplicationState = ReplicationState::default();
        $vm->ProvisioningStatus = VirtualMachineProvisioningStatus::default();
        return $vm;
    }
}