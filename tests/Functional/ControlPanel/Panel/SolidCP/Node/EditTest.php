<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Tests\Functional\ControlPanel\Locations\LocationFixture;
use App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFixture;
use App\Tests\Functional\DbWebTestCase;

final class EditTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/edit');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/edit');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/edit');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Edit SolidCP Server', $crawler->filter('title')->text());
    }

    public function testEdit(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/edit');

        $this->client->submitForm('Edit', [
            'form[id_enterprise_dispatcher]' => EnterpriseDispatcherFixture::EXISTING_ID_DISABLED,
            'form[id_location]' => LocationFixture::EXISTING_ID,
            'form[name]' => $name = 'Edited Test New Node',
            'form[cores]' => $cores = 2,
            'form[threads]' => $threads = 4,
            'form[ram_mb]' => $ram_mb = 1024 * 8,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString($name, $crawler->filter('title')->text());
        $this->assertStringContainsString($name, $crawler->filter('body')->text());
        $this->assertStringContainsString($cores . ' / ' . $threads, $crawler->filter('body')->text());
        $this->assertStringContainsString((string)($ram_mb / 1024), $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/edit');

        $crawler = $this->client->submitForm('Edit', [
            'form[id_enterprise_dispatcher]' => EnterpriseDispatcherFixture::EXISTING_ID_ENABLED,
            'form[id_location]' => LocationFixture::EXISTING_ID,
            'form[name]' => null,
            'form[cores]' => '',
            'form[threads]' => -1,
            'form[ram_mb]' => 0,
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_cores')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_threads')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_ram_mb')->ancestors()->first()->filter('.invalid-feedback')->text());
    }
}