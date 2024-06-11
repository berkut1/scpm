<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Tests\Functional\DbWebTestCase;

final class IndexTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/packages/virtual-machines');
        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdmin(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/packages/virtual-machines');
        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Virtual Machine Packages', $crawler->filter('title')->text());
    }
}