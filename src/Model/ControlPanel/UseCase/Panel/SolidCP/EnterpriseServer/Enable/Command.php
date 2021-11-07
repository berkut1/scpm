<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Enable;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $id_enterprise_server;

    public function __construct(int $id_enterprise_server)
    {
        $this->id_enterprise_server = $id_enterprise_server;
    }
}