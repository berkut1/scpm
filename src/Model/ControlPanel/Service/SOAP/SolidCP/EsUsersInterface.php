<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Service\SOAP\SoapExecuteInterface;

interface EsUsersInterface extends SoapExecuteInterface
{
    public function getUserByUsername(string $username): array;
}