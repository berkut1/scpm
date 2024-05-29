<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Tests\Functional\DbWebTestCase;

final class RenameTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/rename');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/rename');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/rename');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Rename Virtual Machine Package', $crawler->filter('title')->text());
    }

    public function testRename(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/rename');

        $this->client->submitForm('Edit', [
            'form[name]' => 'Renamed Test VM Package RDP23',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Renamed Test VM Package RDP23', $crawler->filter('title')->text());
        $this->assertStringContainsString('Name Renamed Test VM Package RDP23', $crawler->filter('table')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/rename');

        $crawler = $this->client->submitForm('Edit', [
            'form[name]' => '',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());
    }
}