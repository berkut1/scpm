<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Locations;

use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/locations/create');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/locations/create');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/locations/create');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Add Location', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/locations/create');

        $this->client->submitForm('Create', [
            'form[name]' => 'Test Location',
        ]);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Locations', $crawler->filter('title')->text());
        self::assertStringContainsString('Test Location', $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/locations/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => '',
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());
    }

    public function testExists(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/locations/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => 'Exist Test Location',
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('Location with this name already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}