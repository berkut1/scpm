<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Locations;

use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/locations/create');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/locations/create');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/locations/create');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add Location', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/locations/create');

        $this->client->submitForm('Create', [
            'form[name]' => 'Test Location',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Locations', $crawler->filter('title')->text());
        $this->assertStringContainsString('Test Location', $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/locations/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => '',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());
    }

    public function testExists(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/locations/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => 'Exist Test Location',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('Location with this name already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}