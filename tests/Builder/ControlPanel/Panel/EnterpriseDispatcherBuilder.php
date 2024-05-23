<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;

final class EnterpriseDispatcherBuilder
{
    private EnterpriseUserValidator $service;
    private string $name;
    private string $url;
    private string $login;
    private string $password;
    private bool $enabled;
    private ?int $id = null;

    public function __construct(EnterpriseUserValidator $service)
    {
        $this->service = $service;
        $this->name = 'Test Enterprise';
        $this->url = 'http://127.0.0.1:9002';
        $this->login = 'test_reseller';
        $this->password = 'test_password';
        $this->enabled = true;
    }

    public function via(string $name, string $url, string $login, string $password): self
    {
        $clone = clone $this;
        $clone->name = $name;
        $clone->url = $url;
        $clone->login = $login;
        $clone->password = $password;
        return $clone;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function byDefaultDisabled(): self
    {
        $clone = clone $this;
        $clone->enabled = false;
        return $clone;
    }

    public function build(): EnterpriseDispatcher
    {
        $entity = new EnterpriseDispatcher(
            $this->service,
            $this->name,
            $this->url,
            $this->login,
            $this->password,
            $this->enabled,
        );

        if ($this->id !== null) {
            $reflection = new \ReflectionClass($entity);
            $property = $reflection->getProperty('id');
            $property->setValue($entity, $this->id);
        }

        return $entity;
    }
}