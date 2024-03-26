<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Location;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "cp_locations")]
#[ORM\Entity]
class Location implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::INTEGER, nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\Column(name: "name", type: Types::STRING, length: 255, nullable: false)]
    private string $name;

    /** @var Collection|SolidcpServer[] */
    #[ORM\OneToMany(mappedBy: "location", targetEntity: SolidcpServer::class)]
    private array|Collection|ArrayCollection $solidcpServers;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->solidcpServers = new ArrayCollection();
        $this->recordEvent(new Event\LocationCreated($name));
    }

    public function edit(string $name): void
    {
        $oldName = $this->name;
        $this->name = $name;
        $this->recordEvent(new Event\LocationRenamed($oldName, $this));
    }

    public function hasServers(): bool
    {
        return !$this->solidcpServers->isEmpty();
    }

    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
