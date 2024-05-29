<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

final readonly class CustomHttpClient implements CustomHttpClientInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function getHeaders(string $url): array
    {
        try {
            return get_headers($url);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return [];
        }
    }
}