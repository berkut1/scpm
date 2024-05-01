<?php
declare(strict_types=1);

namespace App\Model\AuditLog\UseCase\AuditLog\Remove\Batch;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    #[Assert\NotBlank]
    #[Assert\Type('DateTimeImmutable')]
    public \DateTimeImmutable $startDate;
    #[Assert\NotBlank]
    #[Assert\Type('DateTimeImmutable')]
    public \DateTimeImmutable $endDate;

    public function __construct() {
        $this->endDate = new \DateTimeImmutable("now");
    }

    public static function fromDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate)
    {
        $command = new self();
        $command->startDate = $startDate;
        $command->endDate = $endDate;
        return $command;
    }
}