<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Enable;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $id_solidcp_server;

    public function __construct(int $id_solidcp_server)
    {
        $this->id_solidcp_server = $id_solidcp_server;
    }
}