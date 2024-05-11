<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "user_users")]
#[ORM\Entity]
class User implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(name: "id", type: "user_user_id", nullable: false)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private Id $id;

    #[ORM\Column(name: "login", type: Types::STRING, length: 64, nullable: false)]
    private string $login;

    #[ORM\Column(name: "password", type: Types::STRING, length: 512, nullable: false)]
    private string $password;

    #[ORM\Column(name: "date", type: Types::DATETIME_IMMUTABLE, nullable: false, options: ["default" => "now()"])]
    private \DateTimeImmutable $date;

    #[ORM\Column(name: "role", type: "user_user_role", length: 32, nullable: false)]
    private Role $role;

    #[ORM\Column(name: "status", type: "user_user_status", length: 32, nullable: false)]
    private Status $status;

    private function __construct(Id $id, \DateTimeImmutable $date, string $login)
    {
        $this->id = $id;
        $this->date = $date;
        $this->login = $login;
        $this->role = Role::default();
        $this->recordEvent(new Event\UserCreated($id));
    }

    public static function create(Id $id, \DateTimeImmutable $date, string $login, string $hash): self
    {
        $user = new self($id, $date, $login);
        $user->password = $hash;
        $user->status = Status::active();
        return $user;
    }

    public function edit(string $login): void
    {
        if ($this->login != $login && !$this->role->isAdmin()) {
            throw new \DomainException('You do not have permission for that.');
        }
        $this->login = $login;
    }

    public function changePassword(string $hash): void
    {
        $this->password = $hash;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public function activate(): void
    {
        if ($this->status->isActive()) {
            throw new \DomainException('User is already active.');
        }
        $this->status = Status::active();
    }

    public function suspend(): void
    {
        if ($this->status->isSuspended()) {
            throw new \DomainException('User is already suspended.');
        }
        $this->status = Status::suspended();
    }

    public function archive(): void
    {
        if ($this->status->isArchived()) {
            throw new \DomainException('User is already archived.');
        }
        $this->status = Status::archived();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

}
