<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise = null;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $userId = 0;
    /**
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public int $planId = 0;
    public ?string $spaceName = null;

    public static function create(int $userId, int $solidCpPlanId, ?string $spaceName = null, ?int $id_enterprise = null): self
    {
        $command = new self();
        $command->id_enterprise = $id_enterprise;
        $command->userId = $userId;
        $command->planId = $solidCpPlanId;
        $command->spaceName = $spaceName;
        return $command;
    }
}