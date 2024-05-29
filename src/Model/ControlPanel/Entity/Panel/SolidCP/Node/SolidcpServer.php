<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "cp_solidcp_servers")]
#[ORM\Index(columns: ["id_location"], name: "cp_solidcp_servers_id_location_idx")]
#[ORM\Index(columns: ["id_enterprise_dispatcher"], name: "cp_solidcp_servers_id_enterprise_dispatcher_idx")]
#[ORM\Entity]
class SolidcpServer implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::INTEGER, nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: EnterpriseDispatcher::class, inversedBy: "solidcpServers")]
    #[ORM\JoinColumn(name: "id_enterprise_dispatcher", referencedColumnName: "id", nullable: false)]
    private EnterpriseDispatcher $enterprise;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: "solidcpServers")]
    #[ORM\JoinColumn(name: "id_location", referencedColumnName: "id", nullable: false)]
    private Location $location;

    #[ORM\Column(name: "name", type: Types::STRING, length: 128, nullable: false)]
    private string $name;

    #[ORM\Column(name: "cores", type: Types::INTEGER, nullable: false, options: ["default" => 1])]
    private int $cores;

    #[ORM\Column(name: "threads", type: Types::INTEGER, nullable: false, options: ["default" => 1])]
    private int $threads;

    #[ORM\Column(name: "memory_mb", type: Types::BIGINT, nullable: false, options: ["default" => 1024])]
    private int $memoryMb = 1024;

    #[ORM\Column(name: "enabled", type: Types::BOOLEAN, nullable: false, options: ["default" => 1])]
    private bool $enabled;

    /** @var Collection|SolidcpHostingSpace[] */
    #[ORM\OneToMany(mappedBy: "solidcpServer", targetEntity: SolidcpHostingSpace::class, cascade: ["persist"], orphanRemoval: true)]
    private array|Collection|ArrayCollection $hostingSpaces;

    public function __construct(
        EnterpriseDispatcher $enterprise, Location $location, string $name, int $cores, int $threads, int $memoryMb, bool $enabled = true
    )
    {
        $this->enterprise = $enterprise;
        $this->location = $location;
        $this->name = $name;
        $this->cores = $cores;
        $this->threads = $threads;
        $this->memoryMb = $memoryMb;
        $this->enabled = $enabled;
        $this->hostingSpaces = new ArrayCollection();
        $this->recordEvent(new Event\SolidcpServerCreated($this));
    }

    public function edit(EnterpriseDispatcher $enterprise, Location $location, string $name, int $cores, int $threads, int $memoryMb): void
    {
        $this->enterprise = $enterprise;
        $this->location = $location;
        $this->name = $name;
        $this->cores = $cores;
        $this->memoryMb = $memoryMb;
        $this->threads = $threads;
        $this->recordEvent(new Event\SolidcpServerEdited($this));
    }

    public function disable(): void
    {
        if (!$this->isEnabled()) {
            throw new \DomainException("The Node {$this->getName()} is already disable");
        }
        $this->enabled = false;
        $this->recordEvent(new Event\SolidcpServerDisabled($this));
    }

    public function enable(): void
    {
        if ($this->isEnabled()) {
            throw new \DomainException("The Node {$this->getName()} is already enable");
        }
        $this->enabled = true;
        $this->recordEvent(new Event\SolidcpServerEnabled($this));
    }

    public function hasHostingSpace(): bool
    {
        return !$this->hostingSpaces->isEmpty();
    }

    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEnterprise(): EnterpriseDispatcher
    {
        return $this->enterprise;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCores(): int
    {
        return $this->cores;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    public function getMemoryMb(): int
    {
        return $this->memoryMb;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return SolidcpHostingSpace[]
     */
    public function getHostingSpaces(): array
    {
        return $this->hostingSpaces->toArray();
    }
}
