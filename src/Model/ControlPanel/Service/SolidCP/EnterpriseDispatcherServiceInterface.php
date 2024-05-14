<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

interface EnterpriseDispatcherServiceInterface
{
    public function getEnterpriseDispatcherRealUserId(string $url, string $login, string $password): int;
}