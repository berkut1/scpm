<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Tests\Functional\DbWebTestCase;

final class SetDefaultTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/set-default');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/set-default');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/set-default');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === (string)EnterpriseDispatcherFixture::EXISTING_ID_ENABLED) {
                    $result = $row->text();
                }
            });

        $this->assertStringContainsString('No Set Default', $result);
    }

    public function testSetDefault(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers');
        $removeButton = $crawler->selectButton('Set Default');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/set-default', ['token' => $csrfToken]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === (string)EnterpriseDispatcherFixture::EXISTING_ID_ENABLED) {
                    $result = $row->text();
                }
            });
        $this->assertStringContainsString('http://10.0.0.1:9002 Yes', $result); //after url is Default section
    }
}