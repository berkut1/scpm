<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Tests\Functional\DbWebTestCase;

final class RemoveTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/remove');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/remove');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/remove');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Exist Hosting Space Disabled', $crawler->filter('table > tbody')->text());
    }

    public function testDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces');

        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/remove', ['token' => $csrfToken]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === 'Exist Hosting Space Disabled') {
                    $result = $row->text();
                }
            });

        $this->assertStringNotContainsString('Exist Hosting Space Disabled', $result);
    }

    public function testFaultDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces');

        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/remove', ['token' => $csrfToken]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $result = '';
        $crawler->filter('table > tbody > tr')
            ->each(function ($row) use (&$result) {
                if ($row->filter('td')->first()->text() === 'Exist Hosting Space Enabled') {
                    $result = $row->text();
                }
            });

        $this->assertStringContainsString('Exist Hosting Space Enabled', $result);
        $this->assertStringContainsString('Solidcp Hosting Space Exist Hosting Space Enabled has Plans', $crawler->filter('.alert.alert-danger')->text());
    }
}