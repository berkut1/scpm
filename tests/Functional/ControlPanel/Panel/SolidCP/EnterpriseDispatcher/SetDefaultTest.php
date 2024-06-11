<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Tests\Functional\DbWebTestCase;

final class SetDefaultTest extends DbWebTestCase
{
    private const string URI = '/panel/solidcp/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_DISABLED . '/set-default';

    public function testGuest(): void
    {
        $this->client->request('POST', self::URI);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', self::URI);
        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', self::URI);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === (string)EnterpriseDispatcherFixture::EXISTING_ID_DISABLED) {
                    $result = $row->text();
                }
            });

        self::assertStringContainsString('No Set Default', $result);
    }

    public function testSetDefault(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/enterprise-dispatchers');
        $removeButton = $crawler->selectButton('Set Default');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', self::URI, ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === (string)EnterpriseDispatcherFixture::EXISTING_ID_DISABLED) {
                    $result = $row->text();
                }
            });
        self::assertStringContainsString('http://10.0.0.2:9002 Yes', $result); //after url is Default section
    }
}