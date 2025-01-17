<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Tests\Functional\DbWebTestCase;

final class RemoveTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/remove');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/remove');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/remove');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertStringContainsString('Exist Node Disabled', $crawler->filter('table > tbody')->text());
    }

    public function testDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers');

        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_DISABLED . '/remove', ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === 'Exist Node Disabled') {
                    $result = $row->text();
                }
            });

        self::assertStringNotContainsString('Exist Node Disabled', $result);
    }

    public function testFaultDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers');

        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/remove', ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === 'Exist Node Enabled') {
                    $result = $row->text();
                }
            });

        self::assertStringContainsString('Exist Node Enabled', $result);
        self::assertStringContainsString('Solidcp Server/Node Exist Node Enabled has Hosting Spaces', $crawler->filter('.alert.alert-danger')->text());
    }
}