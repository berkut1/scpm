<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Tests\Functional\DbWebTestCase;

final class EnableTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/enable');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/enable');
        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/enable');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === 'Exist Node Disabled') {
                    $result = $row->text();
                }
            });

        self::assertStringContainsString('No Enable', $result);
    }

    public function testEnable(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers');
        $removeButton = $crawler->selectButton('Enable');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/enable', ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === 'Exist Node Disabled') {
                    $result = $row->text();
                }
            });
        self::assertStringContainsString('Yes Disable', $result);
    }
}