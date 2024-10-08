<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\IsEnable;

final class Command
{
    public ?int $id_enterprise_dispatcher_server = null;

    public function __construct(?int $id_enterprise_dispatcher_server = null)
    {
        $this->id_enterprise_dispatcher_server = $id_enterprise_dispatcher_server;
    }
}