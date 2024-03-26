<?php
declare(strict_types=1);

namespace App\Event\Listener\Security;

use App\Model\AuditLog\Entity\UserId;
use App\Security\UserIdentity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class JWTCreatedListener
{
    public function __construct(private RequestStack $requestStack) {}

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getData();
        $payload['ip'] = $request->getClientIp();
        $payload['id'] = UserId::jwtUserId()->getValue(); //if in event we without reason have different UserIdentity
        if ($event->getUser() instanceof UserIdentity) {
            $payload['id'] = $event->getUser()->getId();
        }
        $event->setData($payload);
    }
}