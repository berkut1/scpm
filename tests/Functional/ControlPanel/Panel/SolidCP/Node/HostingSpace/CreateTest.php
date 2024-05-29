<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Tests\Functional\DbWebTestCase;

final class CreateTest extends DbWebTestCase
{
    public function testGuest(): void
    {
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/create');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testUser(): void
    {
        $this->loginAs('test_user');
        $this->client->request('GET', '/panel/solidcp/hosting-spaces/create');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testGet(): void
    {
        $this->loginAs('test_admin');
        $crawler = $this->client->request('GET', '/panel/solidcp/hosting-spaces/create');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Add Hosting Space', $crawler->filter('title')->text());
    }

    public function testCreate(): void //make a warning - an empty test
    {
        //With WebTestCase we can't test if a form has JavaScript dependent drop down list
        //check it manually.
    }


}