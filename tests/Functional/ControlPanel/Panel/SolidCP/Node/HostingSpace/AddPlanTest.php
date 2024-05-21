<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use App\Tests\Functional\DbWebTestCase;

final class AddPlanTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-plan');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-plan');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $service = $this->getMockBuilder(HostingPlanService::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->atLeastOnce())
            ->method('allNotAddedHostingPlanesFrom')
            ->willReturn([$id = 333 => 'Mock Hosting Plan']);
        self::getContainer()->set(HostingPlanService::class, $service);

        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-plan');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add Plan', $crawler->filter('title')->text());
    }

    public function testAddPlan(): void
    {
        $service = $this->getMockBuilder(HostingPlanService::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->atLeastOnce())
            ->method('allNotAddedHostingPlanesFrom')
            ->willReturn([$id = 333 => 'Mock Hosting Plan']);
        self::getContainer()->set(HostingPlanService::class, $service);

        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-plan');

        $this->client->submitForm('Add', [
            'form[solidcp_id_plan]' => $id,
            'form[name]' => $name = 'Test Plan',
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString($name,
            $crawler->filter('body > div.body.flex-grow-1.px-5 > div > div.card-body > div.box > div:nth-child(2) > div.card-body > table > tbody')->text());
        $this->assertStringContainsString((string)$id,
            $crawler->filter('body > div.body.flex-grow-1.px-5 > div > div.card-body > div.box > div:nth-child(2) > div.card-body > table > tbody')->text());
    }

    public function testExists(): void
    {
        $service = $this->getMockBuilder(HostingPlanService::class)->disableOriginalConstructor()->getMock();
        $service->expects($this->atLeastOnce())
            ->method('allNotAddedHostingPlanesFrom')
            ->willReturn([$id = 333 => 'Mock Hosting Plan']);
        self::getContainer()->set(HostingPlanService::class, $service);

        $this->loginAs('test_admin');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/' . HostingSpaceFixture::EXISTING_ID_ENABLED . '/add-plan');

        $crawler = $this->client->submitForm('Add', [
            'form[solidcp_id_plan]' => $id,
            'form[name]' => $name = 'Exist Hosting Plan',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('SolidcpHostingPlan with this name already exists.', $crawler->filter('.alert.alert-danger')->text());
    }
}