<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_enterprise_dispatcher = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $id_hosting_space = 0;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $packageId = 0;
    public array $osTemplates = [];

    private function __construct(int $id_hosting_space)
    {
        $this->id_hosting_space = $id_hosting_space;
    }

    public static function fromHostingSpace(SolidcpHostingSpace $hostingSpace): self
    {
        $command = new self($hostingSpace->getId());
        $command->id_enterprise_dispatcher = $hostingSpace->getSolidcpServer()->getEnterprise()->getId();
        $command->packageId = $hostingSpace->getSolidCpIdHostingSpace();
        $command->osTemplates = $command->getOsTemplateArray($hostingSpace->getOsTemplates());

        return $command;
    }

    /**
     * @param OsTemplate[] $osTemplates
     * @return array
     */
    private function getOsTemplateArray(array $osTemplates): array
    {
        $array = [];
        foreach ($osTemplates as $osTemplate) {
            $array[] = Collection\Command::create($osTemplate->getFileName(), $osTemplate->getName());
        }
        return $array;
    }
}