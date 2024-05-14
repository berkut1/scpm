<?php
declare(strict_types=1);

namespace App\Service;

interface CustomHttpClientInterface
{
    public function getHeaders(string $url): array;
}