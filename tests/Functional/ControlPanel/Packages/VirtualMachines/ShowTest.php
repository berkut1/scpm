<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Tests\Functional\DbWebTestCase;

final class ShowTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertStringContainsString('Exist Test VM Package RDP23', $crawler->filter('title')->text());
        $this->assertStringContainsString('Name Exist Test VM Package RDP23', $crawler->filter('table')->text());
    }

    public function testNotFound(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/packages/virtual-machines/' . Id::next()->getValue());

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}