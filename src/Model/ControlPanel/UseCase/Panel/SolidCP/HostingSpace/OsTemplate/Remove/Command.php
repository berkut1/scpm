<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Remove;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_hosting_space = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_osTemplate = 0;

    public function __construct(int $id_hosting_space, int $id_osTemplate)
    {
        $this->id_hosting_space = $id_hosting_space;
        $this->id_osTemplate = $id_osTemplate;
    }
}