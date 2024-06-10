<?php
declare(strict_types=1);

namespace Api;

use App\Tests\Functional\DbWebTestCase;

final class HomeTest extends DbWebTestCase
{
    public function testGet(): void
    {
        $this->client->request('GET', '/api/');

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertSame([
            'name' => 'JSON API v1',
        ], $data);
    }

    public function testPost(): void
    {
        $this->client->request('POST', '/api/');

        self::assertSame(405, $this->client->getResponse()->getStatusCode());
    }
}