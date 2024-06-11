<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Tests\Functional\DbWebTestCase;

final class RemovePlanTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/plan/' . HostingPlanFixture::EXISTING_ID . '/remove');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/plan/' . HostingPlanFixture::EXISTING_ID . '/remove');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/plan/' . HostingPlanFixture::EXISTING_ID . '/remove');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertStringContainsString('Exist Hosting Space Enabled', $crawler->filter('table > tbody')->text());
    }

    public function testDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED);

        $removeButton = $crawler->selectButton('Remove');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/plan/' . HostingPlanFixture::EXISTING_ID . '/remove',
            ['token' => $csrfToken]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertStringNotContainsString('Exist Hosting Plan', $crawler->filter('body > div.body.flex-grow-1.px-5 > div > div.card-body > div.box > div:nth-child(2) > div.card-body > table > tbody')->text());
    }
}