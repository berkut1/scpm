<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package\VirtualMachine;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Package\PackageType;
use App\Model\EventsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * VirtualMachinePackage
 *
 * @ORM\Table(
 *     name="cp_package_virtual_machines",
 *     indexes={
 *          @ORM\Index(name="cp_package_virtual_machines_id_package_idx", columns={"id_package"})
 *  })
 * @ORM\Entity
 */
class VirtualMachinePackage implements AggregateRoot
{
    use EventsTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id_package", type="cp_package_id", nullable=false)
     */
    private Id $id;

    /**
     * @ORM\OneToOne(targetEntity=Package::class, cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_package", referencedColumnName="id_package", nullable=false, onDelete="CASCADE")
     * })
     */
    private Package $package;

    /**
     * @ORM\Column(name="cores", type="integer", nullable=false, options={"default"="1"})
     */
    private int $cores = 1;

    /**
     * @ORM\Column(name="threads", type="integer", nullable=false, options={"default"="1"})
     */
    private int $threads = 1;

    /**
     * @ORM\Column(name="ram_mb", type="integer", nullable=false, options={"default"="1024"})
     */
    private int $ramMb = 1024;

    /**
     * @ORM\Column(name="space_gb", type="integer", nullable=false, options={"default"="32"})
     */
    private int $spaceGb = 32;

    /**
     * @ORM\Column(name="iops_min", type="integer", nullable=false, options={"default"="0"})
     */
    private int $iopsMin = 0;

    /**
     * @ORM\Column(name="iops_max", type="integer", nullable=false, options={"default"="0"})
     */
    private int $iopsMax = 0;

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
