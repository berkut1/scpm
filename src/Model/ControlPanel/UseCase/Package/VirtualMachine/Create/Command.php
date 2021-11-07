<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\VirtualMachine\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
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
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $space_gb = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public int $iops_min = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public int $iops_max = 0;
}