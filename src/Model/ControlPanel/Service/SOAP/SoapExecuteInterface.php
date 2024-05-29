<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP;

interface SoapExecuteInterface
{
    public function initManual(
        string $url, string $login, string $password, bool $caching = false, bool $compression = true, bool $keepAlive = false
    ): void;
}