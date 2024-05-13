<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Tests\Builder\User\UserMapper;
use App\Tests\Functional\DbWebTestCase;

final class ShowTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_user');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $crawler = $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('Exist Test VM Package RDP23', $crawler->filter('title')->text());
        $this->assertStringContainsString('Name Exist Test VM Package RDP23', $crawler->filter('table')->text());
    }

    public function testNotFound(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('GET', '/packages/virtual-machines/' . Id::next()->getValue());

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}