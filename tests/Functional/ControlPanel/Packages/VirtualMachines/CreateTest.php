<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Model\User\Entity\User\UserRepository;
use App\Tests\Builder\User\UserMapper;
use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines/create');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_user');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('GET', '/packages/virtual-machines/create');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $crawler = $this->client->request('GET', '/packages/virtual-machines/create');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add Virtual Machine Package', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('GET', '/packages/virtual-machines/create');

        $this->client->submitForm('Create', [
            'form[name]' => 'Test VM Package RDP23',
            'form[cores]' => 2,
            'form[threads]' => 2,
            'form[ram_mb]' => 3072,
            'form[space_gb]' => 80,
            'form[iops_min]' => 0,
            'form[iops_max]' => 3000,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Virtual Machine Packages', $crawler->filter('title')->text());
        $this->assertStringContainsString('Test VM Package RDP23', $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('GET', '/packages/virtual-machines/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => '',
            'form[cores]' => 0,
            'form[threads]' => '',
            'form[ram_mb]' => 3072,
            'form[space_gb]' => 80,
            'form[iops_min]' => 0,
            'form[iops_max]' => -1,
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_cores')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_threads')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should be either positive or zero.', $crawler
            ->filter('#form_iops_max')->ancestors()->first()->filter('.invalid-feedback')->text());
    }

    public function testExists(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin('test_admin');
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));

        $this->client->request('GET', '/packages/virtual-machines/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => 'Exist Test VM Package RDP23',
            'form[cores]' => 2,
            'form[threads]' => 2,
            'form[ram_mb]' => 3072,
            'form[space_gb]' => 80,
            'form[iops_min]' => 0,
            'form[iops_max]' => 3000,
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('VirtualMachinePackage with this name already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}