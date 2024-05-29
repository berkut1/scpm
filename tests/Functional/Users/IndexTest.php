<?php
declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Tests\Functional\DbWebTestCase;

final class IndexTest extends DbWebTestCase
{
    private const string URI = '/users';

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

    public function testAdmin(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', self::URI);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Users', $crawler->filter('title')->text());
    }
}