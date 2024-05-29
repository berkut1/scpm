<?php
declare(strict_types=1);

namespace Api\EnterpriseDispatchers;

use App\Tests\Functional\DbWebTestCase;

final class ShowTest extends DbWebTestCase
{
    private const string URI = '/api/solidCP/enterprise-dispatchers';

    public function testGuest(): void
    {
        $this->client->request('GET', self::URI);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testAllList(): void
    {
        $this->apiLoginAs('test_user');
        $this->client->request('GET', self::URI);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        $expectedData = [
            "1001" => "Exist Test Enterprise Enabled (Login: test_es_login)",
            "1002" => "Exist Test Enterprise Disabled (Login: test_es_login2)",
        ];

        foreach ($data as $object) {
            foreach ($expectedData as $key => $value) {
                self::assertArrayHasKey($key, $object);
                self::assertEquals($value, $object[$key]);
            }
        }
    }
}