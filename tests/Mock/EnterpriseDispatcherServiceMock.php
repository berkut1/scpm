<?php
declare(strict_types=1);

namespace App\Tests\Mock;

use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherServiceInterface;

final class EnterpriseDispatcherServiceMock implements EnterpriseDispatcherServiceInterface
{
    private array $responseMap = [];

    public function addResponse(string $login, int $response): void
    {
        $this->responseMap[$login] = $response;
    }

    public function getEnterpriseDispatcherRealUserId(string $url, string $login, string $password): int
    {
        return $this->responseMap[$login] ?? 1001;
    }
}