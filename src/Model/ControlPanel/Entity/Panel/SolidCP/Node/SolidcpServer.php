<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * SolidcpServer
 *
 * @ORM\Table(
 *     name="cp_solidcp_servers",
 *     indexes={
 *          @ORM\Index(name="cp_solidcp_servers_id_location_idx", columns={"id_location"}),
 *          @ORM\Index(name="cp_solidcp_servers_id_enterprise_idx", columns={"id_enterprise"})
 *  })
 * @ORM\Entity
 */
class SolidcpServer implements AggregateRoot
{
    use EventsTrait;
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=EnterpriseServer::class, inversedBy="solidcpServers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_enterprise", referencedColumnName="id", nullable=false)
     * })
     */
    private EnterpriseServer $enterprise;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="solidcpServers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_location", referencedColumnName="id", nullable=false)
     * })
     */
    private Location $location;

    /**
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="cores", type="integer", nullable=false, options={"default"="1"})
     */
    private int $cores = 1;

    /**
     * @ORM\Column(name="threads", type="integer", nullable=false, options={"default"="1"})
     */
    private int $threads = 1;

    /**
     * @ORM\Column(name="memory_mb", type="bigint", nullable=false, options={"default"="1024"})
     */
    private int $memoryMb = 1024;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default"="1"})
     */
    private bool $enabled;

    /**
     * @var Collection|SolidcpHostingSpace[]
     *
     * @ORM\OneToMany(targetEntity=SolidcpHostingSpace::class,
     *     orphanRemoval=true, cascade={"persist"}, mappedBy="solidcpServer")
     */
    private array|Collection|ArrayCollection $hostingSpaces;

    public function __construct(EnterpriseServer $enterprise, Location $location, string $name, int $cores, int $threads, int $memoryMb, bool $enabled = true)
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

    public function edit(EnterpriseServer $enterprise, Location $location, string $name, int $cores, int $threads, int $memoryMb): void
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
        if(!$this->isEnabled()){
            throw new \DomainException("The Node {$this->getName()} is already disable");
        }
        $this->enabled = false;
        $this->recordEvent(new Event\SolidcpServerDisabled($this));
    }

    public function enable(): void
    {
        if($this->isEnabled()){
            throw new \DomainException("The Node {$this->getName()} is already enable");
        }
        $this->enabled = true;
        $this->recordEvent(new Event\SolidcpServerEnabled($this));
    }

    public function hasHostingSpace(): bool
    {
        return !$this->hostingSpaces->isEmpty();
    }

    #[Pure]
    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEnterprise(): EnterpriseServer
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
