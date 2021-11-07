<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Package
 *
 * @ORM\Table(name="cp_packages")
 * @ORM\Entity
 */
class Package implements AggregateRoot
{
    use EventsTrait;
    /**
     * @ORM\Column(name="id_package", type="cp_package_id", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private Id $id;

    /**
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="package_type", type="cp_package_type", length=512, nullable=false)
     */
    private PackageType $packageType;

    /**
     * @var ArrayCollection|SolidcpHostingPlan[]
     *
     * @ORM\ManyToMany(targetEntity=SolidcpHostingPlan::class, mappedBy="assignedPackages")
     */
    private mixed $solidcpHostingPlans;

    #[Pure]
    public function __construct(Id $id, string $name, PackageType $packageType)
    {
        $this->id = $id;
        $this->name = $name;
        $this->packageType = $packageType;
        $this->solidcpHostingPlans = new ArrayCollection();
    }

    public function changeName(string $name): void
    {
        $oldName = $this->name;
        $this->name = $name;
        $this->recordEvent(new Event\PackageRenamed($this, $oldName));
    }

    public function assignSolidCpPlan(SolidcpHostingPlan $plan): void
    {
        if ($this->solidcpHostingPlans->contains($plan)) {
            return;
        }

        $this->solidcpHostingPlans->add($plan);
        $plan->assignPackage($this);
    }

    public function removeSolidCpPlan(SolidcpHostingPlan $plan)
    {
        if (!$this->solidcpHostingPlans->contains($plan)) {
            return;
        }

        $this->solidcpHostingPlans->removeElement($plan);
        $plan->removePackage($this);
    }

    /**
     * @param SolidcpHostingPlan[] $plans
     */
    public function changeSolidCpPlans(array $plans): void
    {
        $current = $this->solidcpHostingPlans->toArray();
        $new = $plans;

        $compare = static function (SolidcpHostingPlan $a, SolidcpHostingPlan $b): int {
            return $a->getId() <=> $b->getId();
        };

        $removedPlans = [];
        foreach (array_udiff($current, $new, $compare) as $item) { //current - new => diff to del
            //$this->solidcpHostingPlans->removeElement($item);
            $this->removeSolidCpPlan($item);
            $removedPlans[] = $item;
        }

        $addedPlans = [];
        foreach (array_udiff($new, $current, $compare) as $item) { // new - current => diff to add
            //$this->solidcpHostingPlans->add($item);
            $this->assignSolidCpPlan($item);
            $addedPlans[] = $item;
        }

        $this->recordEvent(new Event\PackageChangedSolidCpPlans($this, $removedPlans, $addedPlans));
    }

    #[Pure]
    public function hasAssignedItems(): bool
    {
        return !$this->solidcpHostingPlans->isEmpty();
    }

    #[Pure]
    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPackageType(): PackageType
    {
        return $this->packageType;
    }

    /**
     * @return SolidcpHostingPlan[]
     */
    public function getSolidcpHostingPlans(): array
    {
        return $this->solidcpHostingPlans->toArray();
    }
}