<?php
declare(strict_types=1);

namespace App\Model\AuditLog\UseCase\AuditLog\Add;

use App\Model\AuditLog\Entity\AuditLog;
use App\Model\AuditLog\Entity\AuditLogRepository;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\UserId;
use App\Model\Flusher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

final class Handler
{
    private string $clientIP = '127.0.0.1';

    public function __construct(
        private readonly Flusher            $flusher,
        private readonly Security           $security,
        private readonly RequestStack       $requestStack,
        private readonly AuditLogRepository $auditLogRepository
    )
    {
        if ($this->requestStack->getMainRequest() !== null) {
            $this->clientIP = $this->requestStack->getMainRequest()->getClientIp() ?? '127.0.0.1'; //if null the probably was called from system
        }
    }

    public function handle(Command $command): void
    {
        $executor = $this->security->getUser();
        if ($executor === null) {
            $auditLog = AuditLog::createAsSystem(
                Id::next(),
                $this->clientIP,
                $command->entity,
                $command->taskName,
                $command->records
            );
        } else {
            $userId = new UserId($executor->getId());
            $auditLog = AuditLog::create(
                Id::next(),
                $userId,
                $this->clientIP,
                $command->entity,
                $command->taskName,
                $command->records
            );
        }

        $this->auditLogRepository->add($auditLog);
        $this->flusher->flush();
    }
}