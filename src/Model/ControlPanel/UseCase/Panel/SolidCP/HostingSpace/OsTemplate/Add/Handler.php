<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\Flusher;

final readonly class Handler
{
    public function __construct(
        private Flusher                       $flusher,
        private SolidcpHostingSpaceRepository $hostingSpaceRepository
    ) {}

    public function handle(Command $command): void
    {
        $hostingSpace = $this->hostingSpaceRepository->get($command->id_hosting_space);
        foreach ($command->osTemplates as $template) { //disabled (Collection/Form) form do not get to array $command->osTemplates, so we can ignore check of existing elements.
            $hostingSpace->addOsTemplate($template->path, $template->name);
        }
        $this->flusher->flush($hostingSpace);
    }
}