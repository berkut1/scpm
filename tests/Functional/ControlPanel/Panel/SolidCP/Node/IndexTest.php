<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Tests\Functional\DbWebTestCase;

final class IndexTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/node-servers');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/node-servers');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdmin(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Node Servers', $crawler->filter('title')->text());
    }
}