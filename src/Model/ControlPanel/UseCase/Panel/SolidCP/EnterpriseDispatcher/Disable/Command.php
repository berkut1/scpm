<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Disable;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_enterprise_dispatcher_server = 0;

    public function __construct(int $id_enterprise_dispatcher_server)
    {
        $this->id_enterprise_dispatcher_server = $id_enterprise_dispatcher_server;
    }
}