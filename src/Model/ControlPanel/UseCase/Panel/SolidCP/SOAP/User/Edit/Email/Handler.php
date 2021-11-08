<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Edit\Email;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
    }

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $esUsers = EsUsers::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $user = $esUsers->getUserByUsername($command->username);
        $esUsers->updateUserLiteral(
            $user['UserId'],
            $user['RoleId'],
            $user['StatusId'],
            $user['IsPeer'],
            $user['IsDemo'],
            $user['FirstName'],
            $user['LastName'],
            $command->new_email,
            $user['SecondaryEmail'],
            $user['Address'],
            $user['City'],
            $user['Country'],
            $user['State'],
            $user['Zip'],
            $user['PrimaryPhone'],
            $user['SecondaryPhone'],
            $user['Fax'],
            $user['InstantMessenger'],
            $user['HtmlMail'],
            $user['CompanyName'],
            $user['EcommerceEnabled']
        );
    }
}