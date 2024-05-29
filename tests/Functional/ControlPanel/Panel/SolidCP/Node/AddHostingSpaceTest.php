<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Model\ControlPanel\Service\SolidCP\HostingSpaceService;
use App\Tests\Functional\DbWebTestCase;

final class AddHostingSpaceTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/create-hosting-space');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/create-hosting-space');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $service = $this->getMockBuilder(HostingSpaceService::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->atLeastOnce())
            ->method('allNotAddedHostingSpacesFrom')
            ->willReturn([$solidCpDd = 40 => 'Mock Hosting Space']);
        self::getContainer()->set(HostingSpaceService::class, $service);

        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/create-hosting-space');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add hosting Spaces', $crawler->filter('title')->text());
    }

    public function testAddHostingSpace(): void
    {
        $service = $this->getMockBuilder(HostingSpaceService::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->atLeastOnce())
            ->method('allNotAddedHostingSpacesFrom')
            ->willReturn([$solidCpDd = 40 => 'Mock Hosting Space']);
        self::getContainer()->set(HostingSpaceService::class, $service);

        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/node-servers/' . NodeFixture::EXISTING_ID_ENABLED . '/create-hosting-space');

        $this->client->submitForm('Create', [
            'form[name]' => $name = 'Test New Hosting Space',
            'form[id_hosting_space]' => 40,
            'form[max_active_number]' => 30,
            'form[max_reserved_memory_mb]' => 12,
            'form[space_quota_gb]' => 200,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString($name . ' ' . $solidCpDd,
            $crawler->filter('body > div.body.flex-grow-1.px-5 > div > div.card-body > div.box > div > div.card-body > table > tbody')->text());
    }
}