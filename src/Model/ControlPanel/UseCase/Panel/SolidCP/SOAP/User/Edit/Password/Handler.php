<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Edit\Password;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;

final readonly class Handler
{
    public function __construct(private EnterpriseDispatcherRepository $enterpriseDispatcherRepository) {}

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $esUsers = EsUsers::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $user = $esUsers->getUserByUsername($command->username);
        $esUsers->changeUserPassword(
            $user['UserId'],
            $command->new_password
        );
    }
}