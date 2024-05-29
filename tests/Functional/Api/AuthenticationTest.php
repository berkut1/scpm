<?php
declare(strict_types=1);

namespace Api;

use App\Tests\Functional\DbWebTestCase;

final class AuthenticationTest extends DbWebTestCase
{
    private const string URI = '/api/login/authentication_token';

    public function testGet(): void
    {
        $this->client->request('GET', self::URI);

        self::assertEquals(405, $this->client->getResponse()->getStatusCode());
    }


    public function testGuest(): void
    {
        $this->client->request('POST', self::URI);
        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertEquals('Invalid JSON.', $data['detail']);
    }

    public function testSuccess(): void
    {
        $this->client->request(
            'POST',
            self::URI,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'test_user',
                'password' => 'password',
            ])
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertNotNull($data['token']);
    }

}