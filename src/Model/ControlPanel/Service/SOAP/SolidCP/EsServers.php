<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\NotFoundException;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

final class EsServers extends SoapExecute
{
    public const string SERVICE = 'esServers.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    /**
     * @throws \SoapFault
     */
    public function getMemory(int $serverId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetMemory',
                ['serverId' => $serverId])->GetMemoryResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetMemory Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getMemoryPackageId(int $packageId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetMemoryPackageId',
                ['packageId' => $packageId])->GetMemoryPackageIdResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetMemoryPackageId Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getPackageUnassignedIPAddressesVpsExternalNetwork(int $packageId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetPackageUnassignedIPAddresses',
                [
                    'packageId' => $packageId,
                    'orgId' => 0,
                    'pool' => 'VpsExternalNetwork',
                ])->GetPackageUnassignedIPAddressesResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "getPackageUnassignedIPAddressesVpsExternalNetwork Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function allocatePackageIPAddressesVpsExternalNetwork(
        int   $packageId, bool $allocateRandom, int $addressesNumber,
        array $addressId = []
    ): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'AllocatePackageIPAddresses',
                [
                    'packageId' => $packageId,
                    'orgId' => 0,
                    'groupName' => 'VPS2012',
                    'pool' => 'VpsExternalNetwork',
                    'allocateRandom' => $allocateRandom,
                    'addressesNumber' => $addressesNumber,
                    'addressId' => $addressId, //int array
                ])->AllocatePackageIPAddressesResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "allocatePackageIPAddressesVpsExternalNetwork Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getPackageIPAddressesVpsExternalNetwork(int $packageId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetPackageIPAddresses',
                [
                    'packageId' => $packageId,
                    'orgId' => 0,
                    'pool' => 'VpsExternalNetwork',
                    'filterColumn' => '',
                    'filterValue' => '',
                    'sortColumn' => '',
                    'startRow' => 0,
                    'maximumRows' => 100000000,
                    'recursive' => true,
                ])->GetPackageIPAddressesResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "getPackageIPAddressesVpsExternalNetwork Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }
}