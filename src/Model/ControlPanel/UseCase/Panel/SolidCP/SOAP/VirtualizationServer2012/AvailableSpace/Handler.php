<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\AvailableSpace;

use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\ControlPanel\Entity\AuditLog\EntityType;
use App\Model\ControlPanel\Entity\AuditLog\TaskName;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\UseCase\AuditLog;
use App\Model\ControlPanel\Service\SolidCP\HostingSpaceService;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;

class Handler
{
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private HostingSpaceService $hostingSpaceService;
    private AuditLog\Add\SolidCP\Handler $auditLogHandler;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository, HostingSpaceService $hostingSpaceService, AuditLog\Add\SolidCP\Handler $auditLogHandler)
    {
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->hostingSpaceService = $hostingSpaceService;
        $this->auditLogHandler = $auditLogHandler;
    }

    /**
     * @param Command $command
     * @param array $auditLogRecords
     * @param bool $saveAuditLog
     * @return SolidcpHostingSpace[]
     * @throws \Exception
     */
    public function handle(Command $command, array &$auditLogRecords = [], bool $saveAuditLog = true): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->getDefaultOrById($command->id_enterprise_dispatcher);

        $possibleSpaces = $this->hostingSpaceService->possibleHostingSpacesForInstallation(
            $enterpriseDispatcher->getId(),
            $command->server_location_name,
            $command->server_package_name,
            $command->server_ip_amount,
            $command->getIgnoreNodeIds(),
            $command->getIgnoreHostingSpaceIds());

        $records = [
            Record::create('SOLIDCP_CHECKED_AVAILABLE_VPS_SPACES_FOR_SERVER_PACKAGE_NAME_IN_LOCATION_NAME_INT_RESULT', [
                $command->server_package_name,
                $command->server_location_name,
                count($possibleSpaces),
            ]),
        ];
        if(count($command->getIgnoreHostingSpaceIds())>0){
            $comma_separated = implode(", ", $command->getIgnoreHostingSpaceIds());
            $records[] = Record::create('SOLIDCP_IGNORED_POSSIBLE_SPACE_IDS', [
                $comma_separated
            ]);
        }
        $auditLogRecords = array_merge($auditLogRecords, $records);

        if ($saveAuditLog) {
            $entity = new Entity(EntityType::soapExecute(), Id::zeros()->getValue());
            $auditLogCommand = new AuditLog\Add\SolidCP\Command(
                $enterpriseDispatcher,
                $entity,
                TaskName::checkSolidcpVpsAvailableSpaces(),
                $records
            );
            $this->auditLogHandler->handle($auditLogCommand);
        }

        return $possibleSpaces;
    }
}
