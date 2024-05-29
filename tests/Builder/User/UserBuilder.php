<?php
declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;

final class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;
    private ?string $login = null;
    private ?string $password = null;
    private ?Role $role = null;

    public function __construct()
    {
        $this->id = Id::next();
        $this->date = new \DateTimeImmutable();
        //$this->login = 'login';
    }

    public function viaLogin(string $login = null, string $password_hash = null): self
    {
        $clone = clone $this;
        $clone->login = $login ?? 'viaLogin';
        $clone->password = $password_hash ?? 'hash';
        return $clone;
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withRole(Role $role): self
    {
        $clone = clone $this;
        $clone->role = $role;
        return $clone;
    }

    public function build(): User
    {
        $user = null;

        if ($this->login) {
            $user = User::create(
                $this->id,
                $this->date,
                $this->login,
                $this->password,
            );
        }

        if (!$user) {
            throw new \BadMethodCallException('Specify via method.');
        }

        if ($this->role) {
            $user->changeRole($this->role);
        }

        return $user;
    }
}