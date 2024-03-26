<?php
declare(strict_types=1);

namespace App\Event\Listener\User;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\AuditLog\UseCase\AuditLog;
use App\Model\User\Entity\AuditLog\EntityType;
use App\Model\User\Entity\AuditLog\TaskName;
use App\Model\User\Entity\User\Event\UserCreated;
use App\Model\User\Entity\User\UserRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

final readonly class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security             $security,
        private UserRepository       $userRepository,
        private AuditLog\Add\Handler $auditLogHandler
    ) {}

    #[ArrayShape([UserCreated::class => "string"])]
    #[\Override]
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

        if ($executor === null) {
            $records = [
                Record::create('CREATE_USER_USER', [
                    'System',
                    $user->getLogin(),
                ]),
            ];
        } else {
            $records = [
                Record::create('CREATE_USER_USER', [
                    $executor->getUserIdentifier(),
                    $user->getLogin(),
                ]),
            ];
        }
        $auditLogCommand = new AuditLog\Add\Command($entity, TaskName::createUser(), $records);

        $this->auditLogHandler->handle($auditLogCommand);
    }
}