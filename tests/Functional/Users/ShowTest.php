<?php
declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Tests\Functional\DbWebTestCase;

final class ShowTest extends DbWebTestCase
{
    private const string URI = '/users/' . UserFixture::EXISTING_ID;

    public function testGuest(): void
    {
        $this->client->request('GET', self::URI);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', self::URI);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', self::URI);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('test_user', $crawler->filter('title')->text());
        $this->assertStringContainsString('ID 00000000-0000-0000-0000-000000000001', $crawler->filter('table')->text());
    }

    public function testNotFound(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/users/' . Id::next()->getValue());

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}