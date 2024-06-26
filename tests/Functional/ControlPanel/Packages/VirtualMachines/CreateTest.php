<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines/create');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/packages/virtual-machines/create');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/packages/virtual-machines/create');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Add Virtual Machine Package', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $this->loginAs('test_admin');
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

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Virtual Machine Packages', $crawler->filter('title')->text());
        self::assertStringContainsString('Test VM Package RDP23', $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
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

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_cores')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_threads')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should be either positive or zero.', $crawler
            ->filter('#form_iops_max')->ancestors()->first()->filter('.invalid-feedback')->text());
    }

    public function testExists(): void
    {
        $this->loginAs('test_admin');
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

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('VirtualMachinePackage with this name already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}