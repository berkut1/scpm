<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\User\Entity\User\UserRepository;
use App\Tests\Builder\User\UserMapper;

final class HomeTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('user');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $crawler = $this->client->request('GET', '/');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Home', $crawler->filter('title')->text());
    }

    public function testAdmin(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('berkut');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $crawler = $this->client->request('GET', '/');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Home', $crawler->filter('title')->text());
    }
}