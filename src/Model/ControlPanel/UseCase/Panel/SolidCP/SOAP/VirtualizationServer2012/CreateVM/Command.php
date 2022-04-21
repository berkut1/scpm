<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\CreateVM;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $packageId = 0;

    #[Assert\NotBlank]
    public string $id_package_virtual_machines = '';
//    /**
//     * @Assert\NotBlank()
//     * @Assert\Positive()
//     */
//    public int $cpuCores = 0;
//    /**
//     * @Assert\NotBlank()
//     * @Assert\Positive()
//     */
//    public int $ramSize = 0;
//    /**
//     * @Assert\NotBlank()
//     * @Assert\Positive()
//     */
//    public int $hddSize = 0;
//    /**
//     * @Assert\NotBlank()
//     * @Assert\PositiveOrZero()
//     */
//    public int $hddMinimumIOPS = 0;
//    /**
//     * @Assert\NotBlank()
//     * @Assert\PositiveOrZero()
//     */
//    public int $hddMaximumIOPS = 0;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public int $snapshotsNumber = 0;
    public ?string $hostname = null;

    #[Assert\Type(type: 'bool')]
    public bool $dvdDriveInstalled = true;

    #[Assert\Type(type: 'bool')]
    public bool $bootFromCD = false;

    #[Assert\Type(type: 'bool')]
    public bool $numLockEnabled = false;

    #[Assert\Type(type: 'bool')]
    public bool $startTurnOffAllowed = true;

    #[Assert\Type(type: 'bool')]
    public bool $pauseResumeAllowed = true;

    #[Assert\Type(type: 'bool')]
    public bool $rebootAllowed = true;

    #[Assert\Type(type: 'bool')]
    public bool $resetAllowed = true;

    #[Assert\Type(type: 'bool')]
    public bool $reinstallAllowed = false;

    #[Assert\Type(type: 'bool')]
    public bool $externalNetworkEnabled = true;

    #[Assert\Type(type: 'bool')]
    public bool $privateNetworkEnabled = false;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public int $defaultaccessvlan = 0;


    #[Assert\NotBlank]
    public string $osTemplateFile = "";

    #[Assert\NotBlank]
    public string $password = "";

    #[Assert\Type(type: 'bool')]
    public bool $summaryLetterEmail = false;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public int $externalAddressesNumber = 1;

    #[Assert\Type(type: 'bool')]
    public bool $randomExternalAddresses = true;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public int $privateAddressesNumber = 0;

    #[Assert\Type(type: 'bool')]
    public bool $randomPrivateAddresses = false;

    public static function createDefault(int $packageId, string $id_package_virtual_machines, string $osTemplateFile, string $password, int $externalAddressesNumber, ?int $id_enterprise_dispatcher = null):self
    {
        $command = new self();
        $command->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $command->packageId = $packageId;
        $command->id_package_virtual_machines = $id_package_virtual_machines;
        $command->osTemplateFile = $osTemplateFile;
        $command->password = $password;
        $command->externalAddressesNumber = $externalAddressesNumber;
        return $command;
    }
}