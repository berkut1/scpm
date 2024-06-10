<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\ChangePlans\SolidCP;

use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    private readonly ?string $id_package;

    #[Assert\Positive]
    public ?array $id_plans = [];

    #[Assert\NotBlank]
    private ?string $packageType = null;

    private function __construct(string $id_package)
    {
        $this->id_package = $id_package;
    }

    public static function fromPackage(Package $package): self
    {
        $command = new self($package->getId()->getValue());
        $command->packageType = $package->getPackageType()->getName();
        $command->id_plans = array_map(static function (SolidcpHostingPlan $solidcpHostingPlan): int {
            return $solidcpHostingPlan->getId();
        }, $package->getSolidcpHostingPlans());
        return $command;
    }

    public function getIdPackage(): string
    {
        return $this->id_package;
    }

}