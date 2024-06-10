<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

final class EsServers extends EnterpriseSoapServiceFactory
{
    protected const string SERVICE = 'esServers.asmx';

    /**
     * @throws \SoapFault
     */
    public function getMemory(int $serverId): array
    {
        try {
            return $this->convertArray($this->execute(
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
                'GetPackageUnassignedIPAddresses',
                [
                    'packageId' => $packageId,
                    'orgId' => 0,
                    'pool' => 'VpsExternalNetwork',
                ])->GetPackageUnassignedIPAddressesResult);
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