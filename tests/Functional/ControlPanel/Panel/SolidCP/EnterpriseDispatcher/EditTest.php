<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Tests\Functional\DbWebTestCase;

final class EditTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Edit Enterprise Dispatcher', $crawler->filter('title')->text());
    }

    public function testEdit(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        $this->setCustomEnterpriseDispatcherRealUserIdRespond(
            $login = 'rename_login', 12345);
        $this->setCustomHttpClientRespond(
            $url = 'http://10.0.10.10:9002', ['HTTP/1.1 200 OK']);

        $this->client->submitForm('Edit', [
            'form[name]' => $name = 'Renamed Exist Test Enterprise Enabled',
            'form[url]' => $url,
            'form[login]' => $login,
            'form[password]' => 'rename_password',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Enterprise Dispatchers', $crawler->filter('title')->text());
        $this->assertStringContainsString($name, $crawler->filter('body')->text());
        $this->assertStringContainsString($url, $crawler->filter('body')->text());
    }

    public function testNotValid(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        $crawler = $this->client->submitForm('Edit', [
            'form[name]' => '',
            'form[url]' => null,
            'form[login]' => '',
            'form[password]' => '',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_name')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_url')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_login')->ancestors()->first()->filter('.invalid-feedback')->text());

        $this->assertStringContainsString('This value should not be blank.', $crawler
            ->filter('#form_password')->ancestors()->first()->filter('.invalid-feedback')->text());
    }
}