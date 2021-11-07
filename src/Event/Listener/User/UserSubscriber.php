<?php 
declare(strict_types=1);

namespace App\Event\Listener\User;

use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\User\Entity\AuditLog\EntityType;
use App\Model\User\Entity\AuditLog\TaskName;
use App\Model\User\Entity\User\Event\UserCreated;
use App\Model\User\Entity\User\UserRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class UserSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private UserRepository $userRepository;
    private RequestStack $requestStack;
    private AuditLog\Add\Handler $auditLogHandler;
    private string $clientIP = '127.0.0.1';

    public function __construct(Security $security, UserRepository $userRepository, RequestStack $requestStack, AuditLog\Add\Handler $auditLogHandler)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
        $this->auditLogHandler = $auditLogHandler;
        if($this->requestStack->getMasterRequest() !== null){
            $this->clientIP = $this->requestStack->getMasterRequest()->getClientIp() ?? '127.0.0.1'; //if null the probably was called from system
        }
    }
    #[ArrayShape([UserCreated::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            UserCreated::class => 'onUserCreated',
        ];
    }

    public function onUserCreated(UserCreated $event): void
    {
        $executor = $this->security->getUser();

        $user = $this->userRepository->get($event->id_createdUser);
        $entity = new Entity(EntityType::userUser(), $user->getId()->getValue());

        if($executor === null){
            $records = [
                Record::create('CREATE_USER_USER', [
                    'System',
                    $user->getLogin()
                ]),
            ];
        }else{
            $records = [
                Record::create('CREATE_USER_USER', [
                    $executor->getUsername(),
                    $user->getLogin()
                ]),
            ];
        }
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createUser(), $records);

        $this->auditLogHandler->handle($auditLogCommand);
    }
}