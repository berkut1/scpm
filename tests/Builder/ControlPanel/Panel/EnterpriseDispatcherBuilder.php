<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;

final class EnterpriseDispatcherBuilder
{
    private EnterpriseUserValidator $service;
    private string $name = 'Test Enterprise';
    private string $url = 'http://127.0.0.1:9002';
    private string $login = 'test_reseller';
    private string $password = 'test_password';
    private bool $enabled = true;
    private ?int $id = null;

    public function __construct(EnterpriseUserValidator $service)
    {
        $this->service = $service;
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