<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Locations;

use App\Tests\Functional\DbWebTestCase;

final class IndexTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/locations');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/locations');
        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdmin(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/locations');
        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Locations', $crawler->filter('title')->text());
    }
}