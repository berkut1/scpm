<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\AggregateRoot;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: "cp_solidcp_enterprise_dispatchers")]
#[ORM\UniqueConstraint(name: "cp_solidcp_enterprise_dispatchers_unique_default", columns: ["id"], options: ["where" => "is_default"])]
#[ORM\Entity]
class EnterpriseDispatcher implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\Column(name: "name", type: "string", length: 128, nullable: false)]
    private string $name;

    #[ORM\Column(name: "url", type: "string", length: 1024, nullable: false)]
    private string $url;

    #[ORM\Column(name: "login", type: "string", length: 64, nullable: false)]
    private string $login;

    #[ORM\Column(name: "password", type: "string", length: 512, nullable: false)]
    private string $password;

    #[ORM\Column(name: "solidcp_login_id", type: "integer", nullable: false)]
    private int $solidcpLoginId;

    #[ORM\Column(name: "is_default", type: "boolean", nullable: false, options: ["default" => 0])]
    private bool $isDefault;

    #[ORM\Column(name: "enabled", type: "boolean", nullable: false, options: ["default" => 1])]
    private bool $enabled;

    /** @var Collection|SolidcpServer[] */
    #[ORM\OneToMany(mappedBy: "enterprise", targetEntity: SolidcpServer::class)]
    private array|Collection|ArrayCollection $solidcpServers;

    public function __construct(EnterpriseDispatcherService $service, string $name, string $url, string $login, string $password, bool $enabled = true)
    {
        $this->name = $name;
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
//        $this->solidcpLoginId = $solidcpLoginId;
        $this->solidcpLoginId = $service->getEnterpriseDispatcherRealUserId($url, $login, $password);
        $this->isDefault = false;
        $this->enabled = $enabled;
        $this->solidcpServers = new ArrayCollection();
        $this->recordEvent(new Event\EnterpriseDispatcherCreated($this));
    }

    public function edit(EnterpriseDispatcherService $service, string $name, string $url, string $login, string $password): void
    {
        $this->name = $name;
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->solidcpLoginId = $service->getEnterpriseDispatcherRealUserId($url, $login, $password);
        $this->recordEvent(new Event\EnterpriseDispatcherEdited($this));
    }

    public function disable(): void
    {
        if (!$this->isEnabled()) {
            throw new \DomainException("The Enterprise Dispatcher {$this->getName()} is already disable");
        }
        $this->enabled = false;
        $this->recordEvent(new Event\EnterpriseDispatcherDisabled($this));
    }

    public function enable(): void
    {
        if ($this->isEnabled()) {
            throw new \DomainException("The Enterprise Dispatcher {$this->getName()} is already enable");
        }
        $this->enabled = true;
        $this->recordEvent(new Event\EnterpriseDispatcherEnabled($this));
    }

    public function hasServers(): bool
    {
        return !$this->solidcpServers->isEmpty();
    }

    #[Pure]
    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function setDefault(): void
    {
        $this->isDefault = true;
    }

    public function setNonDefault(): void
    {
        $this->isDefault = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSolidcpLoginId(): int
    {
        return $this->solidcpLoginId;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
