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

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', self::URI);

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', self::URI);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('test_user', $crawler->filter('title')->text());
        self::assertStringContainsString('ID 00000000-0000-0000-0000-000000000001', $crawler->filter('table')->text());
    }

    public function testNotFound(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/users/' . Id::next()->getValue());

        self::assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}