<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $id_enterprise_dispatcher = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $id_location = 0;
    /**
     * @Assert\NotBlank()
     */
    public string $name = '';
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $cores = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $threads = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $ram_mb = 0;
}