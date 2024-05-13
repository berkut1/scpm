<?php
declare(strict_types=1);

namespace App\Tests\Builder\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;

final class EnterpriseDispatcherBuilder
{
    private EnterpriseDispatcherService $service;
    private string $name;
    private string $url;
    private string $login;
    private string $password;
    private bool $enabled;

    public function __construct(EnterpriseDispatcherService $service)
    {
        $this->service = $service;
        $this->name = 'Test Enterprise';
        $this->url = 'http://127.0.0.1:9002';
        $this->login = 'test_reseller';
        $this->password = 'test_password';
        $this->enabled = true;
    }

    public function via(string $name, string $url,string $login, string $password): self
    {
        $clone = clone $this;
        $clone->name = $name;
        $clone->url = $url;
        $clone->login = $login;
        $clone->password = $password;
        return $clone;
    }

    public function build(): EnterpriseDispatcher
    {
        return new EnterpriseDispatcher(
            $this->service,
            $this->name,
            $this->url,
            $this->login,
            $this->password,
        );
    }
}