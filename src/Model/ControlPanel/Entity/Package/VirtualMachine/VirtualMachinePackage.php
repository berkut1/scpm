<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package\VirtualMachine;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Package\PackageType;
use App\Model\EventsTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "cp_package_virtual_machines")]
#[ORM\Index(columns: ["id_package"], name: "cp_package_virtual_machines_id_package_idx")]
#[ORM\Entity]
class VirtualMachinePackage implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(name: "id_package", type: "cp_package_id", nullable: false)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private Id $id;

    #[ORM\OneToOne(targetEntity: Package::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: "id_package", referencedColumnName: "id_package", nullable: false, onDelete: "CASCADE")]
    private Package $package;

    #[ORM\Column(name: "cores", type: Types::INTEGER, nullable: false, options: ["default" => 1])]
    private int $cores;

    #[ORM\Column(name: "threads", type: Types::INTEGER, nullable: false, options: ["default" => 1])]
    private int $threads;

    #[ORM\Column(name: "ram_mb", type: Types::INTEGER, nullable: false, options: ["default" => 1024])]
    private int $ramMb;

    #[ORM\Column(name: "space_gb", type: Types::INTEGER, nullable: false, options: ["default" => 32])]
    private int $spaceGb;

    #[ORM\Column(name: "iops_min", type: Types::INTEGER, nullable: false, options: ["default" => 0])]
    private int $iopsMin;

    #[ORM\Column(name: "iops_max", type: Types::INTEGER, nullable: false, options: ["default" => 0])]
    private int $iopsMax;

    public function __construct(Id $id, string $name, int $cores, int $threads, int $ramMb, int $spaceGb, int $iopsMin, int $iopsMax)
    {
        $this->id = $id;
        $this->package = new Package($id, $name, PackageType::virtualMachine());
        $this->cores = $cores;
        $this->threads = $threads;
        $this->ramMb = $ramMb;
        $this->spaceGb = $spaceGb;
        $this->iopsMin = $iopsMin;
        $this->iopsMax = $iopsMax;
        $this->recordEvent(new Event\VirtualMachinePackageCreated($this));
    }

    public function edit(int $cores, int $threads, int $ramMb, int $spaceGb, int $iopsMin, int $iopsMax): void
    {
        $this->cores = $cores;
        $this->threads = $threads;
        $this->ramMb = $ramMb;
        $this->spaceGb = $spaceGb;
        $this->iopsMin = $iopsMin;
        $this->iopsMax = $iopsMax;
        $this->recordEvent(new Event\VirtualMachinePackageEdited($this));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getCores(): int
    {
        return $this->cores;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    public function getRamMb(): int
    {
        return $this->ramMb;
    }

    public function getSpaceGb(): int
    {
        return $this->spaceGb;
    }

    public function getIopsMin(): int
    {
        return $this->iopsMin;
    }

    public function getIopsMax(): int
    {
        return $this->iopsMax;
    }
}
