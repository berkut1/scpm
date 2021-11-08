<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackageRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServerRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsServers;
use App\Model\EntityNotFoundException;
use App\ReadModel\ControlPanel\Location\LocationFetcher;
use Doctrine\DBAL\Connection;

class HostingSpaceService
{
    private Connection $connection;
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private LocationFetcher $locationFetcher;
    private VirtualMachinePackageRepository $virtualMachinePackageRepository;
    private SolidcpServerRepository $serverRepository;

    public function __construct(Connection                      $connection,
                                EnterpriseDispatcherRepository  $enterpriseDispatcherRepository,
                                LocationFetcher                 $locationFetcher,
                                VirtualMachinePackageRepository $virtualMachinePackageRepository,
                                SolidcpServerRepository         $serverRepository)
    {
        $this->connection = $connection;
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->locationFetcher = $locationFetcher;
        $this->virtualMachinePackageRepository = $virtualMachinePackageRepository;
        $this->serverRepository = $serverRepository;
    }

    //$idEnterprise - means a Reseller with his hosting spaces
    public function allNotAddedHostingSpacesFrom(int $id_enterprise_dispatcher): array
    {
        return $this->allNotAddedHostingSpacesFromInternal($id_enterprise_dispatcher);
    }

    public function allNotAddedHostingSpacesExceptHostingSpaceIdFrom(int $id_enterprise_dispatcher, int $exceptHostingSpaceId): array
    {
        return $this->allNotAddedHostingSpacesFromInternal($id_enterprise_dispatcher, $exceptHostingSpaceId);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Exception
     */
    private function allNotAddedHostingSpacesFromInternal(int $id_enterprise_dispatcher, int $exceptHostingSpaceId = 0): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($id_enterprise_dispatcher);
        $esPackages = EsPackages::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $spaces = [];
        foreach ($esPackages->getUserPackages($enterpriseDispatcher->getSolidcpLoginId()) as $value) {
            $spaces[(int)$value['PackageId']] = "{$value['PackageName']} - id:{$value['PackageId']}";
        }
        //dump($spaces);
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'solidcp_id_hosting_space',
                'spaces.name'
            )
            ->from('cp_solidcp_hosting_spaces', 'spaces')
            ->leftJoin('spaces', 'cp_solidcp_servers', 'servers', 'servers.id = spaces.id_server')
            ->where('servers.id_enterprise_dispatcher = :id_enterprise_dispatcher')
            ->setParameter('id_enterprise_dispatcher', $id_enterprise_dispatcher)
//            ->where("NOT (id_hosting_space = ANY (string_to_array(:ids,',')::int[]))") //NOT IN ARRAY
//            ->setParameter('ids', implode(',', array_keys($spaces)))
            ->orderBy('name')
            ->executeQuery(); //execute() deprecated https://github.com/doctrine/dbal/pull/4578thub.com/doctrine/dbal/pull/4578;

        $existSpaces = array_column($stmt->fetchAllAssociative(), 'name', 'solidcp_id_hosting_space');
        if ($exceptHostingSpaceId > 0) {
            $existSpaces = array_diff_key($existSpaces, [$exceptHostingSpaceId => 'remove it from this array']);
        }

        return array_diff_key($spaces, $existSpaces); //remove exist keys from $spaces
    }

    /**
     * @param int $id_enterprise_dispatcher
     * @param string $location_name
     * @param string $server_package_name
     * @param int $ip_amount
     * @param int[] $ignore_node_ids
     * @param int[] $ignore_hosting_space_ids
     * @return SolidcpHostingPlan[]
     * @throws \Exception
     */
    public function possibleHostingSpacesWithPlansForInstallation(int $id_enterprise_dispatcher, string $location_name, string $server_package_name, int $ip_amount, array $ignore_node_ids, array $ignore_hosting_space_ids): array
    {
        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($id_enterprise_dispatcher);
        if (!$enterpriseDispatcher->isEnabled()) {
            return []; //obviously if disable then no available spaces. => empty array
        }

        $hosting_space_ids_from_node_id = $this->getSolidCpHostingSpaceIdsFromNodeIds($ignore_node_ids);
        $ignore_hosting_space_ids = array_merge($ignore_hosting_space_ids, $hosting_space_ids_from_node_id);

        $location = $this->locationFetcher->getByName($location_name);
        $package = $this->virtualMachinePackageRepository->getByName($server_package_name);
        $possiblePlans = $package->getPackage()->getSolidcpHostingPlans();

        $esServers = EsServers::createFromEnterpriseDispatcher($enterpriseDispatcher);
        $possibleSpacesAndPlans = [];
        $esPackages = EsPackages::createFromEnterpriseDispatcher($enterpriseDispatcher);
        //searching possible spaces with plans for installation
        foreach ($possiblePlans as $possiblePlan) {
            $solidcpHostingSpace = $possiblePlan->getHostingSpace();
            $ignoreHostingSpace = false;
            foreach ($ignore_hosting_space_ids as $one) {
                if ($one === $solidcpHostingSpace->getSolidCpIdHostingSpace()) {
                    $ignoreHostingSpace = true;
                    break;
                }
            }
            if($ignoreHostingSpace){
                continue;
            }
            $ips = $esServers->getPackageUnassignedIPAddressesVpsExternalNetwork($possiblePlan->getHostingSpace()->getSolidCpIdHostingSpace());
            /*thanks for this awful code - data return SolidCP*/
            if(!isset($ips['PackageIPAddress'])){
                continue;
            }
            if(isset($ips['PackageIPAddress'][0])){ //we can get different arrays, so if it is a true array, check that exist 0 index
                if(count($ips['PackageIPAddress']) < $ip_amount){
                    continue;
                }
            }else{ //if we get assoc array, that means there is only 1 ip left
                if(1 < $ip_amount){
                    continue;
                }
            }

            $isEnabled = ($solidcpHostingSpace->getSolidcpServer()->isEnabled() && $solidcpHostingSpace->isEnabled());

            if ($isEnabled && $solidcpHostingSpace->getSolidcpServer()->getLocation()->getId() === $location->getId()) {
                //Check Free RAM
                $memory = $esServers->getMemoryPackageId($solidcpHostingSpace->getSolidCpIdHostingSpace());
                $freeMemory = (int)$memory['FreePhysicalMemoryKB'] - ($package->getRamMb() * 1024);
                if ($freeMemory >= $possiblePlan->getHostingSpace()->getMaxReservedMemoryKb()) {
                    //Get current active spaces
                    $packageDataSet = $esPackages->getNestedPackagesSummary($solidcpHostingSpace->getSolidCpIdHostingSpace());
                    $countOfActivePackage = 0;
                    if($packageDataSet['NewDataSet']['Table']['PackagesNumber'] > 0){ //if we have packages, then get number of Active Packages
                        $summary = $esPackages->getNestedPackagesSummary($solidcpHostingSpace->getSolidCpIdHostingSpace())['NewDataSet']['Table1'];
                        /*thanks for this awful code - data return SolidCP*/
                        if (isset($summary[0])) { //we can get different arrays from getNestedPackagesSummary
                            foreach ($summary as $one) {
                                if ($one['StatusID'] === 1) {
                                    $countOfActivePackage = $one['PackagesNumber'];
                                    break;
                                }
                            }
                            unset($one);
                        } else {
                            if ($summary['StatusID'] === 1) {
                                $countOfActivePackage = $summary['PackagesNumber'];
                            }
                        }
                    }

                    if ($countOfActivePackage < $solidcpHostingSpace->getMaxActiveNumber() + 1) {
                        $possibleSpacesAndPlans[] = $possiblePlan;
                    }
                }
            }
        }

        return $possibleSpacesAndPlans;
    }

    private function getSolidCpHostingSpaceIdsFromNodeIds(array $ignore_node_ids): array
    {
        $hosting_space_ids_from_node_id = [];
        foreach ($ignore_node_ids as $one) {
            try {
                $server = $this->serverRepository->get($one);
                $spaceIds = [];
                foreach ($server->getHostingSpaces() as $hostingSpace) {
                    $spaceIds[] = $hostingSpace->getSolidCpIdHostingSpace();
                }
                unset($hostingSpace);
                $hosting_space_ids_from_node_id = array_merge($hosting_space_ids_from_node_id, $spaceIds);
            } catch (EntityNotFoundException $e) {
                //ignore if not found $server
            }
        }
        unset($one);
        return $hosting_space_ids_from_node_id;
    }
}