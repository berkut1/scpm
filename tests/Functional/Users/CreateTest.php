<?php
declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Model\User\Entity\User\Role;
use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    private const string URI = '/users/create';
    
    public function testGuest(): void
    {
        $this->client->request('GET', self::URI);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', self::URI);

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', self::URI);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Add User', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', self::URI);

        $this->client->submitForm('Create', [
            'form[login]' => $name ='Test New User',
            'form[password]' => 'testPassword',
            'form[role]' => Role::USER,
        ]);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Users', $crawler->filter('title')->text());
        self::assertStringContainsString($name, $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', self::URI);

        $crawler = $this->client->submitForm('Create', [
            'form[login]' => '',
            'form[password]' => '12345',
            'form[role]' => Role::USER,
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_login')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value is too short. It should have 8 characters or more.', $crawler
            ->filter('#form_password')->ancestors()->first()->filter('.invalid-feedback')->text());
    }

    public function testExists(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', self::URI);

        $crawler = $this->client->submitForm('Create', [
            'form[login]' => 'test_user',
            'form[password]' => 'password',
            'form[role]' => Role::USER,
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('User with this login already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}