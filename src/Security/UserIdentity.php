<?php
declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\User\Status;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserIdentity implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private string $id,
        private string $username,
        private string $password,
        private string $role,
        private string $status
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->status === Status::STATUS_ACTIVE;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    #[\Override]
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    #[\Override]
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    #[\Override]
    public function getSalt(): ?string
    {
        return null;
    }

    #[\Override]
    public function eraseCredentials(): void {}

    #[\Override]
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id &&
            $this->password === $user->password &&
            $this->role === $user->role &&
            $this->status === $user->status;
    }
}
