<?php
declare(strict_types=1);

namespace App\Event\Listener\Security;

use App\Model\AuditLog\Entity\AuditLog;
use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\Entity\UserId;
use App\Model\Flusher;
use App\Model\User\Entity\AuditLog\AuditLogRepository;
use App\Model\User\Entity\AuditLog\EntityType;
use App\Model\User\Entity\AuditLog\TaskName;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class LoginListener
{
    private Flusher $flusher;
    private AuditLogRepository $auditLogRepository;
    private RequestStack $requestStack;
    private string $clientIP = '127.0.0.1';

    public function __construct(Flusher $flusher, AuditLogRepository $auditLogRepository, RequestStack $requestStack)
    {
        $this->flusher = $flusher;
        $this->auditLogRepository = $auditLogRepository;
        $this->requestStack = $requestStack;
        if($this->requestStack->getMainRequest() !== null){
            $this->clientIP = $this->requestStack->getMainRequest()->getClientIp() ?? '127.0.0.1'; //if null the probably was called from system
        }
    }

    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $authenticationToken = $event->getAuthenticator();
        $request = $event->getRequest();
        $login = 'n/a';
        if($authenticationToken instanceof LoginFormAuthenticator){
            $login = $request->get('login');
        }else{
            $login = json_decode($request->getContent(), true)['username'];
        }

        $entity = new Entity(EntityType::userUser(), UserId::systemUserId()->getValue());
        $log = AuditLog::createAsSystem(Id::next(),
            $this->clientIP, $entity, TaskName::loginUser(), [
                Record::create('LOGIN_USER_FAILED_FROM_IP', [
                    $login,
                    $this->clientIP
                ])
            ]);

        $this->auditLogRepository->add($log);
        $this->flusher->flush();
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        if($event->getAuthenticationToken() instanceof \Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken){
            return; //we don't want that this event create logs for every API calls.
        }
        // Get the User entity.
        $user = $event->getAuthenticationToken()->getUser();

        $entity = new Entity(EntityType::userUser(), $user->getId());
        $log = AuditLog::createAsSystem(Id::next(),
            $this->clientIP, $entity, TaskName::loginUser(), [
                Record::create('LOGIN_USER_FROM_IP', [
                    $user->getUserIdentifier(),
                    $this->clientIP
                ])
            ]);

        $this->auditLogRepository->add($log);
        $this->flusher->flush();
    }
}