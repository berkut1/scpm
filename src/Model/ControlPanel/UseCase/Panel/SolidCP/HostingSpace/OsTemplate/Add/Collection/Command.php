<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add\Collection;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $path = '';
    /**
     * @Assert\NotBlank()
     */
    public string $name = '';

    public static function create(string $path, string $name): self
    {
        $command = new self();
        $command->path = $path;
        $command->name = $name;
        return $command;
    }
}