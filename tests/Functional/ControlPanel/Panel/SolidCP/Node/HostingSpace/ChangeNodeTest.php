<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFixture;
use App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\NodeFixture;
use App\Tests\Functional\DbWebTestCase;

final class ChangeNodeTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/change-node');

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/change-node');

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/change-node');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Change Hosting Space Node', $crawler->filter('title')->text());
    }

    public function testChangeNode(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/change-node');

        $this->client->submitForm('Change', [
            'form[id_enterprise_dispatcher]' => EnterpriseDispatcherFixture::EXISTING_ID_ENABLED,
            'form[id_server]' => NodeFixture::EXISTING_ID_DISABLED,
        ]);

        self::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Exist Hosting Space Enabled', $crawler->filter('title')->text());
        self::assertStringContainsString('Exist Node Disabled', $crawler->filter('body')->text());
    }
}