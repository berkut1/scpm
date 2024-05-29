<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Tests\Functional\DbWebTestCase;

final class EnableTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/enable');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/enable');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/enable');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();


        $this->assertStringContainsString('No Enable', $crawler->filter('body')->text());
    }

    public function testEnable(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED);
        $removeButton = $crawler->selectButton('Enable');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_DISABLED . '/enable', ['token' => $csrfToken]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Yes Disable', $crawler->filter('body')->text());
    }
}