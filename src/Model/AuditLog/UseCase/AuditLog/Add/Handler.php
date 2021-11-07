<?php
declare(strict_types=1);

namespace App\Model\AuditLog\UseCase\AuditLog\Add;

use App\Model\AuditLog\Entity\AuditLog;
use App\Model\AuditLog\Entity\AuditLogRepository;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\UserId;
use App\Model\Flusher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class Handler
{
    private Flusher $flusher;
    private Security $security;
    private RequestStack $requestStack;
    private AuditLogRepository $auditLogRepository;
    private string $clientIP = '127.0.0.1';

    public function __construct(Flusher $flusher, Security $security, RequestStack $requestStack, AuditLogRepository $auditLogRepository)
    {
        $this->flusher = $flusher;
        $this->security = $security;
        $this->requestStack = $requestStack;
        if($this->requestStack->getMasterRequest() !== null){
            $this->clientIP = $this->requestStack->getMasterRequest()->getClientIp() ?? '127.0.0.1'; //if null the probably was called from system
        }
        $this->auditLogRepository = $auditLogRepository;
    }

    public function handle(Command $command): void
    {
        $executor = $this->security->getUser();
        if($executor === null){
            $auditLog = AuditLog::createAsSystem(
                Id::next(),
                $this->clientIP,
                $command->entity,
                $command->taskName,
                $command->records
            );
        }else{
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