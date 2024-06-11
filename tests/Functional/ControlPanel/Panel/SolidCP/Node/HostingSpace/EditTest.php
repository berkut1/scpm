<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Tests\Functional\DbWebTestCase;

final class EditTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/edit');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/edit');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/edit');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Edit Hosting Space', $crawler->filter('title')->text());
    }

    public function testEdit(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/edit');

        $this->client->submitForm('Edit', [
            'form[name]' => $name = 'Edited Test New Hosting Space',
            'form[max_active_number]' => $max_active_number = 100,
            'form[max_reserved_memory_mb]' => $max_reserved_memory_mb = 400,
            'form[space_quota_gb]' => $space_quota_gb = 1000,
        ]);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString($name, $crawler->filter('title')->text());
        self::assertStringContainsString($name, $crawler->filter('body')->text());
        self::assertStringContainsString((string)$max_active_number, $crawler->filter('body')->text());
        self::assertStringContainsString((string)($max_reserved_memory_mb), $crawler->filter('body')->text());
        self::assertStringContainsString((string)($space_quota_gb), $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/edit');

        $crawler = $this->client->submitForm('Edit', [
            'form[name]' => '',
            'form[max_active_number]' => -1,
            'form[max_reserved_memory_mb]' => 'gfg',
            'form[space_quota_gb]' => null,
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should be positive.', $crawler
            ->filter('#form_max_active_number')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('Please enter an integer.', $crawler
            ->filter('#form_max_reserved_memory_mb')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_space_quota_gb')->ancestors()->first()->filter('.invalid-feedback')->text());
    }
}