<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\NotFoundException;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsServers;
use Doctrine\DBAL\Connection;

class ServerService
{
    private Connection $connection;
    private EnterpriseServerRepository $enterpriseServerRepository;

    public function __construct(Connection $connection, EnterpriseServerRepository $enterpriseServerRepository)
    {
        $this->connection = $connection;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
    }

    public function allIPAddressesVpsExternalNetwork(int $id_enterprise): array
    {
        $enterpriseServer = $this->enterpriseServerRepository->get($id_enterprise);
        $esServer = EsServers::createFromEnterpriseServer($enterpriseServer);

        return $esServer->getPackageIPAddressesVpsExternalNetwork(1); //1 is the system package of all Panel package
    }

    public function ipAddressVpsExternalNetworkDetails(int $id_enterprise, string $ipAddress): array
    {
        $ips = $this->allIPAddressesVpsExternalNetwork($id_enterprise);
        if($ips['Count'] > 1){
            foreach ($ips['Items']['PackageIPAddress'] as $ip){
                if($ip['ExternalIP'] === $ipAddress){
                    if($ip['ItemId'] === 0){
                        throw new NotFoundException('The IP is not assigned to a VM.');
                    }
                    return $ip;
                }
            }
        }else{
            $ip = $ips['Items']['PackageIPAddress'];
            if($ip['ExternalIP'] === $ipAddress){
                if($ip['ItemId'] === 0){
                    throw new NotFoundException('The IP is not assigned to a VM.');
                }
                return $ip;
            }
        }

        throw new NotFoundException('The IP is not assigned to a VM.');
        /*        array:13 [▼
                "PackageAddressID" => 314
                "AddressID" => 6
                "ExternalIP" => "10.20.30.90"
                "InternalIP" => ""
                "SubnetMask" => "255.255.255.0"
                "DefaultGateway" => "10.20.30.1"
                "ItemId" => 0
                "IsPrimary" => false
                "PackageId" => 2
                "PackageName" => "test10"
                "UserId" => 2
                "UserName" => "reseller"
                "VLAN" => 0
              ]*/
    }
}