<?php
declare(strict_types=1);

namespace App\Service;

interface CustomHttpClientInterface
{
    /**
     * Tries to fetch the headers of the specified URL.
     * Returns an array containing the headers if successful, otherwise returns an empty array.
     *
     * @param string $url The URL to fetch headers from.
     * @return array The headers of the URL, or an empty array if an error occurs.
     */
    public function getHeaders(string $url): array;
}