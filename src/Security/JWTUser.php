<?php
declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

readonly class JWTUser implements JWTUserInterface
{
    public function __construct(
        private string $username,
        private string $id = '',
        private string $ip = '',
        private array  $roles = []
    ) {}

    #[\Override]
    public static function createFromPayload($username, array $payload): self
    {
        if (isset($payload['roles'])) {
            return new static($username, $payload['id'], $payload['ip'], (array)$payload['roles']);
        }

        return new static($username);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    #[\Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    #[\Override]
    public function getPassword(): null
    {
        return null;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    #[\Override]
    public function getSalt(): null
    {
        return null;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    #[\Override]
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    #[\Override]
    public function eraseCredentials() {}
}