<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Model\User\Entity\User\UserRepository;
use App\Tests\Builder\User\UserMapper;
use App\Tests\Functional\DbWebTestCase;

final class RemoveTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_user');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Exist Test VM Package RDP23', $crawler->filter('body')->text());
    }

    public function testDelete(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $crawler = $this->client->request('GET', '/packages/virtual-machines');
        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove', ['token' => $csrfToken]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        
        $this->assertStringNotContainsString('Exist Test VM Package RDP23', $crawler->filter('body')->text());
    }
}