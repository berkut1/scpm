<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/create');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/create');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/create');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Add Enterprise Dispatcher', $crawler->filter('title')->text());
    }

    public function testCreate(): void
    {
        $service = $this->getMockBuilder(EnterpriseUserValidator::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->once())
            ->method('getEnterpriseDispatcherRealUserId')
            ->willReturn(12333);
        self::getContainer()->set(EnterpriseUserValidator::class, $service);

        $this->loginAs('test_admin');

        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/create');

        $this->setCustomHttpClientRespond('http://127.0.0.2:9003', ['HTTP/1.1 200 OK']);

        $this->client->submitForm('Create', [
            'form[name]' => $name = 'Test Enterprise Dispatcher',
            'form[url]' => 'http://127.0.0.2:9003',
            'form[login]' => 'test_login',
            'form[password]' => 'test_password',
        ]);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Enterprise Dispatchers', $crawler->filter('title')->text());
        self::assertStringContainsString($name, $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/create');

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => '',
            'form[url]' => '',
            'form[login]' => null,
            'form[password]' => '',
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_url')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_login')->ancestors()->first()->filter('.invalid-feedback')->text());

        self::assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_password')->ancestors()->first()->filter('.invalid-feedback')->text());
    }

    public function testExists(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/create');

        //$this->setCustomHttpClientRespond('http://127.0.0.2:9003', ['HTTP/1.1 200 OK']);

        $crawler = $this->client->submitForm('Create', [
            'form[name]' => 'Exist Test Enterprise Enabled',
            'form[url]' => 'http://127.0.0.2:9003',
            'form[login]' => 'test_login',
            'form[password]' => 'test_password',
        ]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('EnterpriseDispatcher with this name already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}