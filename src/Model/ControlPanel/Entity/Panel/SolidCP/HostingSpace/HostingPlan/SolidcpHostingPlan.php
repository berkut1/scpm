<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SolidcpHostingPlan
 *
 * @ORM\Table(
 *     name="cp_solidcp_hosting_plans",
 *     indexes={
 *          @ORM\Index(name="cp_solidcp_hosting_plans_id_hosting_space_idx", columns={"id_hosting_space"})
 *  })
 * @ORM\Entity
 */
class SolidcpHostingPlan implements AggregateRoot
{
    use EventsTrait;
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=SolidcpHostingSpace::class, inversedBy="hostingPlans")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_hosting_space", referencedColumnName="id", nullable=false)
     * })
     */
    private SolidcpHostingSpace $hostingSpace;

    /**
     * @ORM\Column(name="solidcp_id_plan", type="integer", nullable=false)
     */
    private int $solidcpIdPlan;

    /**
     * @ORM\Column(name="solidcp_id_server", type="integer", nullable=false)
     */
    private int $solidcpIdServer;

    /**
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @var Collection|Package[]
     *
     * @ORM\ManyToMany(targetEntity=Package::class, inversedBy="solidcpHostingPlans")
     * @ORM\JoinTable(name="cp_package_assigned_scp_hosting_plans",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_plan", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_package", referencedColumnName="id_package", nullable=false)
     *   }
     * )
     */
    private array|Collection|ArrayCollection $assignedPackages;

    public function __construct(SolidcpHostingSpace $hostingSpace, int $solidcpIdPlan, HostingPlanService $hostingPlanService, string $name)
    {
        $this->hostingSpace = $hostingSpace;
        $this->solidcpIdPlan = $solidcpIdPlan;
        $this->solidcpIdServer = $hostingPlanService->getRealSolidCpServerIdFromPlanId($hostingSpace, $solidcpIdPlan);
        $this->name = $name;
        $this->assignedPackages = new ArrayCollection();
        $this->recordEvent(new Event\SolidcpHostingPlanCreated($this));
    }

    public function assignPackage(Package $package): void
    {
        if ($this->assignedPackages->contains($package)) {
            return;
        }

        $this->assignedPackages->add($package);
        $package->assignSolidCpPlan($this);
    }

    public function removePackage(Package $package)
    {
        if (!$this->assignedPackages->contains($package)) {
            return;
        }

        $this->assignedPackages->removeElement($package);
        $package->removeSolidCpPlan($this);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHostingSpace(): SolidcpHostingSpace
    {
        return $this->hostingSpace;
    }

    public function getSolidcpIdPlan(): int
    {
        return $this->solidcpIdPlan;
    }

    public function getSolidcpIdServer(): int
    {
        return $this->solidcpIdServer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Package[]
     */
    public function getAssignedPackages(): array
    {
        return $this->assignedPackages->toArray();
    }
}
