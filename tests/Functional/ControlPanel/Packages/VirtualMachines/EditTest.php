<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Tests\Functional\DbWebTestCase;

final class EditTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/edit');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/edit');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/edit');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Edit Virtual Machine Package', $crawler->filter('title')->text());
    }

    public function testEdit(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/edit');

        $this->client->submitForm('Edit', [
            'form[cores]' => 1,
            'form[threads]' => 1,
            'form[ram_mb]' => $ram_mb = 2048,
            'form[space_gb]' => $space_gb = 40,
            'form[iops_min]' => $iops_min = 10,
            'form[iops_max]' => $iops_max = 1500,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Exist Test VM Package RDP23', $crawler->filter('title')->text());
        $this->assertStringContainsString('Cores/Threads: 1/1', $crawler->filter('body')->text());
        $this->assertStringContainsString('RAM ' . $ram_mb, $crawler->filter('body')->text());
        $this->assertStringContainsString('Space ' . $space_gb, $crawler->filter('body')->text());
        $this->assertStringContainsString('IOPS Min: ' . $iops_min . ' / Max: ' . $iops_max, $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/edit');

        $crawler = $this->client->submitForm('Edit', [
            'form[cores]' => -1,
            'form[threads]' => 0,
            'form[ram_mb]' => '',
            'form[space_gb]' => 80,
            'form[iops_min]' => -1,
            'form[iops_max]' => null,
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_cores')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_threads')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_ram_mb')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should be either positive or zero.', $crawler
            ->filter('#form_iops_min')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_iops_max')->ancestors()->first()->filter('.invalid-feedback')->text());
    }
}