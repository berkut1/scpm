<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use App\Tests\Functional\DbWebTestCase;

final class AddOsTemplateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-os-template');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-os-template');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $service = $this->getMockBuilder(VirtualizationServer2012Service::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->atLeastOnce())
            ->method('allOsTemplateListFrom')
            ->willReturn([$path = 'test.vhdx' => 'Test Os Template']);
        self::getContainer()->set(VirtualizationServer2012Service::class, $service);

        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-os-template');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add Os Template', $crawler->filter('title')->text());
    }

    public function testAddOs(): void
    {
        $this->markTestSkipped("With WebTestCase we can't test if a form has JavaScript dependent drop down list, check it manually");
    }
}