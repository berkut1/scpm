<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Tests\Functional\DbWebTestCase;

final class RemoveTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');

        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loginAs('test_admin');
        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Exist Test VM Package RDP23', $crawler->filter('body')->text());
    }

    public function testDelete(): void
    {
        $this->loginAs('test_admin');

        $crawler = $this->client->request('GET', '/packages/virtual-machines');
        $removeButton = $crawler->selectButton('REMOVE');
        $form = $removeButton->form([], 'POST');
        $csrfToken = $form->getValues()['token'];

        $this->client->request('POST', '/packages/virtual-machines/' . VmPackageFixture::EXISTING_ID . '/remove', ['token' => $csrfToken]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertStringNotContainsString('Exist Test VM Package RDP23', $crawler->filter('body')->text());
    }
}