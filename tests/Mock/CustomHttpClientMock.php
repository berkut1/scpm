<?php
declare(strict_types=1);

namespace App\Tests\Mock;

use App\Service\CustomHttpClientInterface;

final class CustomHttpClientMock implements CustomHttpClientInterface
{
    private array $responseMap = [];

    public function addResponse(string $url, array $response): void
    {
        $this->responseMap[$url] = $response;
    }

    public function getHeaders(string $url): array
    {
        return $this->responseMap[$url] ?? [];
    }
}