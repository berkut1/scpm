<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Tests\Functional\DbWebTestCase;

final class ShowTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('Exist Node Enabled', $crawler->filter('title')->text());
        $this->assertStringContainsString('Name Exist Node Enabled', $crawler->filter('table')->text());
    }

    public function testNotFound(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/node-servers/' . 9999999);

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}