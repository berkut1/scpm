<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\EntityNotFoundException;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "cp_solidcp_hosting_spaces")]
#[ORM\Index(columns: ["id_server"], name: "cp_solidcp_hosting_spaces_id_server_idx")]
#[ORM\Entity]
class SolidcpHostingSpace implements AggregateRoot
{
    use EventsTrait;

    private const int PSQL_INT_MAX = 2147483647;

    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::INTEGER, nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: SolidcpServer::class, inversedBy: "hostingSpaces")]
    #[ORM\JoinColumn(name: "id_server", referencedColumnName: "id", nullable: false)]
    private SolidcpServer $solidcpServer;

    #[ORM\Column(name: "solidcp_id_hosting_space", type: Types::INTEGER, nullable: false)]
    private int $solidCpIdHostingSpace;

    #[ORM\Column(name: "name", type: Types::STRING, length: 128, nullable: false)]
    private string $name;

    #[ORM\Column(name: "max_active_number", type: Types::INTEGER, nullable: false)]
    private int $maxActiveNumber;

    #[ORM\Column(name: "max_reserved_memory_kb", type: Types::INTEGER, nullable: false)]
    private int $maxReservedMemoryKb;

    #[ORM\Column(name: "space_quota_gb", type: Types::INTEGER, nullable: false)]
    private int $spaceQuotaGb;

    #[ORM\Column(name: "enabled", type: Types::BOOLEAN, nullable: false, options: ["default" => 1])]
    private bool $enabled;

    /** @var Collection|SolidcpHostingPlan[] */
    #[ORM\OneToMany(mappedBy: "hostingSpace", targetEntity: SolidcpHostingPlan::class, cascade: ["persist"], orphanRemoval: true)]
    private array|Collection|ArrayCollection $hostingPlans;

    /** @var Collection|OsTemplate[] */
    #[ORM\OneToMany(mappedBy: "hostingSpace", targetEntity: OsTemplate::class, cascade: ["persist"], orphanRemoval: true)]
    private array|Collection|ArrayCollection $osTemplates;

    public function __construct(
        SolidcpServer $solidcpServer, int $solidCpIdHostingSpace, string $name, int $maxActiveNumber, int $maxReservedMemoryKb,
        int           $spaceQuotaGb, bool $enabled = true
    )
    {
        $this->solidcpServer = $solidcpServer;
        $this->solidCpIdHostingSpace = $solidCpIdHostingSpace;
        $this->name = $name;
        $this->maxActiveNumber = $maxActiveNumber;
        if($maxReservedMemoryKb > self::PSQL_INT_MAX){
            throw new \DomainException("This value $maxReservedMemoryKb is too big than MAX ". self::PSQL_INT_MAX);
        }
        $this->maxReservedMemoryKb = $maxReservedMemoryKb;
        $this->spaceQuotaGb = $spaceQuotaGb;
        $this->enabled = $enabled;
        $this->hostingPlans = new ArrayCollection();
        $this->osTemplates = new ArrayCollection();
        $this->recordEvent(new Event\SolidcpHostingSpaceCreated($this));
    }

    public function edit(string $name, int $maxActiveNumber, int $maxReservedMemoryKb, int $spaceQuotaGb): void
    {
        $this->name = $name;
        $this->maxActiveNumber = $maxActiveNumber;
        $this->maxReservedMemoryKb = $maxReservedMemoryKb;
        $this->spaceQuotaGb = $spaceQuotaGb;
        $this->recordEvent(new Event\SolidcpHostingSpaceEdited($this));
    }

    public function changeServer(SolidcpServer $newSolidcpServer): void
    {
        if ($newSolidcpServer->getId() === $this->solidcpServer->getId()) { //nothing changed
            return;
        }
        $oldNodeName = $this->solidcpServer->getName();
        $this->solidcpServer = $newSolidcpServer;
        $this->recordEvent(new Event\SolidcpHostingSpaceChangedNode($this, $oldNodeName, $newSolidcpServer->getName()));
    }

    public function changSolidCpHostingSpace(int $newSolidCpIdHostingSpace): void
    {
        if ($newSolidCpIdHostingSpace === $this->solidCpIdHostingSpace) { //nothing changed
            return;
        }
        $oldSolidCpIdHostingSpace = $this->solidCpIdHostingSpace;
        $this->solidCpIdHostingSpace = $newSolidCpIdHostingSpace;
        $this->recordEvent(new Event\SolidcpHostingSpaceChangedSolidCpHostingSpaceId($this, $oldSolidCpIdHostingSpace, $newSolidCpIdHostingSpace));
    }

    public function addHostingPlan(SolidcpHostingPlan $solidcpHostingPlan): void
    {
        foreach ($this->hostingPlans as $current) {
            if ($current->getSolidcpIdPlan() === $solidcpHostingPlan->getSolidcpIdPlan()) {
                throw new \DomainException('Hosting Plan already exists');
                //return;
            }
        }
        $this->hostingPlans->add($solidcpHostingPlan);
        $this->mergeEventsFrom($solidcpHostingPlan);
    }

    public function removeHostingPlan(int $idHostingPlan): void
    {
        foreach ($this->hostingPlans as $current) {
            if ($idHostingPlan === $current->getId()) {
                $this->hostingPlans->removeElement($current);
                $this->recordEvent(new Event\SolidcpHostingSpaceRemovedPlan($this, $current->getName()));
                return;
            }
        }
        throw new EntityNotFoundException('Hosting Plan was not found');
    }

    public function addOsTemplate(string $fileName, string $name): void
    {
        foreach ($this->osTemplates as $current) {
            if ($current->getFileName() === $fileName) {
                throw new \DomainException("TemplateOs $name already added");
            }
        }
        $osTemplate = new OsTemplate($this, $fileName, $name);
        $this->osTemplates->add($osTemplate);
        $this->recordEvent(new Event\SolidcpHostingSpaceAddedOsTemplate($this, $osTemplate));
    }

    public function removeOsTemplate(int $id): void
    {
        foreach ($this->osTemplates as $current) {
            if ($current->getId() === $id) {
                $this->osTemplates->removeElement($current);
                $this->recordEvent(new Event\SolidcpHostingSpaceRemovedOsTemplate($this, $current));
                return;
            }
        }
        throw new EntityNotFoundException('TemplateOs was not found');
    }

    public function disable(): void
    {
        if (!$this->isEnabled()) {
            throw new \DomainException("The Hosting Space {$this->getName()} is already disable");
        }
        $this->enabled = false;
        $this->recordEvent(new Event\SolidcpHostingSpaceDisabled($this));
    }

    public function enable(): void
    {
        if ($this->isEnabled()) {
            throw new \DomainException("The Hosting Space {$this->getName()} is already enable");
        }
        $this->enabled = true;
        $this->recordEvent(new Event\SolidcpHostingSpaceEnabled($this));
    }

    public function hasPlans(): bool
    {
        return !$this->hostingPlans->isEmpty();
    }

    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSolidcpServer(): SolidcpServer
    {
        return $this->solidcpServer;
    }

    /**
     * for SolidCP panel
     * @return int
     */
    public function getSolidCpIdHostingSpace(): int
    {
        return $this->solidCpIdHostingSpace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMaxActiveNumber(): int
    {
        return $this->maxActiveNumber;
    }

    public function getMaxReservedMemoryKb(): int
    {
        return $this->maxReservedMemoryKb;
    }

    public function getSpaceQuotaGb(): int
    {
        return $this->spaceQuotaGb;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return SolidcpHostingPlan[]
     */
    public function getHostingPlans(): array
    {
        return $this->hostingPlans->toArray();
    }

    /**
     * @return OsTemplate[]
     */
    public function getOsTemplates(): array
    {
        return $this->osTemplates->toArray();
    }

    public function getOsTemplateByName(string $name): ?OsTemplate
    {
        foreach ($this->getOsTemplates() as $template) {
            if ($template->getName() === $name) {
                return $template;
            }
        }
        return null;
    }
}
