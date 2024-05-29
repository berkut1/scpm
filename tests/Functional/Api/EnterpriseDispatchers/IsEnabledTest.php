<?php
declare(strict_types=1);

namespace Api\EnterpriseDispatchers;

use App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFixture;
use App\Tests\Functional\DbWebTestCase;

final class IsEnabledTest extends DbWebTestCase
{
    private const string URI = '/api/solidCP/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_ENABLED . '/is-enable';

    public function testGuest(): void
    {
        $this->client->request('GET', self::URI);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testIsEnabled(): void
    {
        $this->apiLoginAs('test_user');
        $this->client->request('GET', self::URI);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertEquals([
            'is_enable' => true,
        ], $data);
    }

    public function testIsDisabled(): void
    {
        $this->apiLoginAs('test_user');
        $this->client->request('GET', '/api/solidCP/enterprise-dispatchers/' . EnterpriseDispatcherFixture::EXISTING_ID_DISABLED . '/is-enable');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertEquals([
            'is_enable' => false,
        ], $data);
    }

    public function testIsEnabledDefault(): void
    {
        $this->apiLoginAs('test_user');
        $this->client->request('GET', '/api/solidCP/enterprise-dispatchers/default/is-enable');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertEquals([
            'is_enable' => true,
        ], $data);
    }

    public function testFault(): void
    {
        $this->apiLoginAs('test_user');
        $this->client->request('GET', '/api/solidCP/enterprise-dispatchers/' . 9999 . '/is-enable');

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertEquals(400, $data['error']['code']);
        self::assertEquals('EnterpriseDispatcher is not found.', $data['error']['message']);
    }
}