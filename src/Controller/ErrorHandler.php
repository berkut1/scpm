<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;

final readonly class ErrorHandler
{
    public function __construct(private LoggerInterface $logger) {}

    public function handle(\DomainException $e): void
    {
        $this->logger->warning($e->getMessage(), ['exception' => $e]);
    }

    public function handleSoap(\SoapFault $e): void
    {
        $this->logger->error($e->getMessage(), ['exception' => $e]);
    }

    public function handleError(\Exception $e): void
    {
        $this->logger->error($e->getMessage(), ['exception' => $e]);
    }
}
