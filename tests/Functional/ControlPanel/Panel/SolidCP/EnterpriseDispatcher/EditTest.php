<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Service\SolidCP\EnterpriseUserValidator;
use App\Tests\Functional\DbWebTestCase;

final class EditTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Edit Enterprise Dispatcher', $crawler->filter('title')->text());
    }

    public function testEdit(): void
    {
        $service = $this->getMockBuilder(EnterpriseUserValidator::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->once())
            ->method('getEnterpriseDispatcherRealUserId')
            ->willReturn(12345);
        self::getContainer()->set(EnterpriseUserValidator::class, $service);

        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/edit');

        $this->setCustomHttpClientRespond(
            $url = 'http://10.0.10.10:9002', ['HTTP/1.1 200 OK']);

        $this->client->submitForm('Edit', [
            'form[name]' => $name = 'Renamed Exist Test Enterprise Enabled',
            'form[url]' => $url,
            'form[login]' => 'rename_login',
            'form[password]' => 'rename_password',
        ]);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Enterprise Dispatchers', $crawler->filter('title')->text());
        self::assertStringContainsString($name, $crawler->filter('body')->text());
        self::assertStringContainsString($url, $crawler->filter('body')->text());
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
}