<?php
declare(strict_types=1);

namespace App\Tests\Mock;

use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherServiceInterface;

final class EnterpriseDispatcherServiceMock implements EnterpriseDispatcherServiceInterface
{
    public function getEnterpriseDispatcherRealUserId(string $url, string $login, string $password): int
    {
        return 1001;
    }
}