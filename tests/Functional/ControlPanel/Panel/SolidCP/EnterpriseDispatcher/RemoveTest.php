<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Tests\Functional\DbWebTestCase;

final class RemoveTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/remove');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_DISABLED . '/remove');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_DISABLED . '/remove');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertStringContainsString('Exist Test Enterprise Enabled', $crawler->filter('table > tbody')->text());
    }

    public function testDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers');

        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_DISABLED . '/remove', ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === (string)EnterpriseDispatcherFixture::EXISTING_ID_DISABLED) {
                    $result = $row->text();
                }
            });

        self::assertStringNotContainsString((string)EnterpriseDispatcherFixture::EXISTING_ID_DISABLED, $result);
    }

    public function testFaultDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers');

        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/remove', ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === (string)EnterpriseDispatcherFixture::EXISTING_ID_ENABLED) {
                    $result = $row->text();
                }
            });

        self::assertStringContainsString((string)EnterpriseDispatcherFixture::EXISTING_ID_ENABLED, $result);
        self::assertStringContainsString('Enterprise Dispatcher Exist Test Enterprise Enabled assigned to Nodes', $crawler->filter('.alert.alert-danger')->text());
    }
}