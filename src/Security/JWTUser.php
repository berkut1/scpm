<?php
declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class JWTUser implements JWTUserInterface
{
    private string $username;
    private string $id;
    private string $ip;
    private array $roles;

    public function __construct(string $username, string $id = '', string $ip = '', array $roles = [])
    {
        $this->username = $username;
        $this->id = $id;
        $this->ip = $ip;
        $this->roles = $roles;
    }

    public static function createFromPayload($username, array $payload): self
    {
        if (isset($payload['roles'])) {
            return new static($username, $payload['id'], $payload['ip'], (array) $payload['roles']);
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }
}