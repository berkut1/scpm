<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsPackages;
use Doctrine\DBAL\Connection;

class HostingPlanService
{
    private Connection $connection;
    private EnterpriseServerRepository $enterpriseServerRepository;
    private SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository;
    private EsPackages $esPackages;

    public function __construct(Connection $connection,
                                EnterpriseServerRepository $enterpriseServerRepository,
                                SolidcpHostingSpaceRepository $solidcpHostingSpaceRepository,
                                EsPackages $esPackages)
    {
        $this->connection = $connection;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->solidcpHostingSpaceRepository = $solidcpHostingSpaceRepository;
        $this->esPackages = $esPackages;
    }

    public function getRealSolidCpServerIdFromPlanId(SolidcpHostingSpace $hostingSpace, int $plan_id): int
    {
        $enterpriseServer = $hostingSpace->getSolidcpServer()->getEnterprise();
        $this->esPackages->initFromEnterpriseServer($enterpriseServer);
        try {
            $plan = $this->esPackages->getHostingPlan($plan_id);
        } catch (\Exception $e) {
            throw new \DomainException("Soap execution error (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
        return (int)$plan['ServerId'];
    }

    //$idEnterprise - means a Reseller with his hosting space
    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function allNotAddedHostingPlacesFrom(int $idHostingSpace): array
    {
        $solidcpHostingSpace = $this->solidcpHostingSpaceRepository->get($idHostingSpace);
        $esPackages = EsPackages::createFromEnterpriseServer($solidcpHostingSpace->getSolidcpServer()->getEnterprise());
        $userID = $solidcpHostingSpace->getSolidcpServer()->getEnterprise()->getSolidcpLoginId();
        $solidCpPlans = $esPackages->getHostingPlans($userID)['NewDataSet']['Table'];
        //dump($solidCpPlans);

        $plans = [];
        foreach ($solidCpPlans as $value){
            if((int)$value['PackageID'] === $solidcpHostingSpace->getSolidCpIdHostingSpace()){ //show only plans, which assigned to specific hosting space
                $plans[(int)$value['PlanID']] = "{$value['PlanName']} - id:{$value['PlanID']}";
            }
        }

        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'solidcp_id_plan',
                'plans.name'
            )
            ->from('cp_solidcp_hosting_plans', 'plans')
            ->leftJoin('plans', 'cp_solidcp_hosting_spaces', 'spaces', 'spaces.id = plans.id_hosting_space')
            ->leftJoin('spaces', 'cp_solidcp_servers', 'servers', 'servers.id = spaces.id_server')
            ->where('servers.id_enterprise = :id_enterprise')
            ->setParameter('id_enterprise', $solidcpHostingSpace->getSolidcpServer()->getEnterprise()->getId())
            ->andWhere('spaces.solidcp_id_hosting_space = :solidcp_id_hosting_space')
            ->setParameter('solidcp_id_hosting_space', $solidcpHostingSpace->getSolidCpIdHostingSpace())

            ->orderBy('name')
            ->executeQuery(); //execute() deprecated https://github.com/doctrine/dbal/pull/4578thub.com/doctrine/dbal/pull/4578;

        $existSpaces = array_column($stmt->fetchAllAssociative(), 'name','solidcp_id_plan');

        return array_diff_key($plans, $existSpaces); //remove exist keys from $plans
    }
}