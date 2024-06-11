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

        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        self::assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        self::assertStringContainsString('Exist Test VM Package RDP23', $crawler->filter('title')->text());
        self::assertStringContainsString('Name Exist Test VM Package RDP23', $crawler->filter('table')->text());
    }

    public function testNotFound(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('GET', '/packages/virtual-machines/' . Id::next()->getValue());

        self::assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}