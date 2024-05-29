<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\ChangeStatus;

use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SolidCP\ServerService;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package;

final readonly class Handler
{
    public function __construct(
        private EnterpriseDispatcherRepository $enterpriseDispatcherRepository,
        private ServerService                  $serverService,
        private Package\ChangeStatus\Handler   $changeStatusHandler
    ) {}

    public function handle(Command $command): void
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);
        if (!$enterpriseDispatcher->isEnabled()) {
            throw new \DomainException("The EnterpriseDispatcher {$enterpriseDispatcher->getName()} is disabled");
        }

        $ip = $this->serverService->ipAddressVpsExternalNetworkDetails($enterpriseDispatcher->getId(), $command->vps_ip_address);
        if ($ip['UserName'] === $enterpriseDispatcher->getLogin()) {
            throw new \LogicException("You can not change status of yourself package");
        }
        if ($ip['UserName'] !== $command->client_login) {
            throw new \DomainException("This IP is currently owned by client {$ip['UserName']}, not client {$command->client_login}");
        }

        $records[] = Record::create('SOLIDCP_CHANGED_NAME_VPS_WITH_IP_TO_STATUS', [
            $command->client_login,
            $command->vps_ip_address,
            $command->vps_status,
        ]);

        $changeStatusCommand = new Package\ChangeStatus\Command($ip['PackageId'], $command->vps_status, $enterpriseDispatcher->getId());
        $this->changeStatusHandler->handle($changeStatusCommand, $records);
    }
}
