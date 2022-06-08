<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\VirtualizationServer2012\VirtualMachineRequestedState;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine\VirtualMachine;
use App\Model\ControlPanel\Service\NotFoundException;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

final class EsVirtualizationServer2012 extends SoapExecute
{
    public const SERVICE = 'esVirtualizationServer2012.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    public function generateMacAddress(): string
    {
        try {
            return $this->execute(
                self::SERVICE,
                'GenerateMacAddress', [])->GenerateMacAddressResult;
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GenerateMacAddress Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    public function changeVirtualMachineState(int $itemId, VirtualMachineRequestedState $state): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'ChangeVirtualMachineState', [
                    'itemId' => $itemId,
                    'state' => $state
                ])->ChangeVirtualMachineStateResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("ChangeVirtualMachineState Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    public function getOperatingSystemTemplates(int $packageId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'GetOperatingSystemTemplates',
                    [
                        'packageId' => $packageId
                    ])->GetOperatingSystemTemplatesResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetOperatingSystemTemplates Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    public function createNewVirtualMachine(VirtualMachine $vmSettings,
                                            string         $osTemplateFile,
                                            string         $password,
                                            int            $externalAddressesNumber = 0,
                                            bool           $randomExternalAddresses = false,
                                            array          $externalAddresses = [],
                                            int            $privateAddressesNumber = 0,
                                            bool           $randomPrivateAddresses = false,
                                            array          $privateAddresses = [],
                                            ?string        $summaryLetterEmail = null): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'CreateNewVirtualMachine',
                    [
                        'VMSettings' => $vmSettings,
                        'osTemplateFile' => $osTemplateFile,
                        'password' => $password,
                        'summaryLetterEmail' => $summaryLetterEmail,
                        'externalAddressesNumber' => $externalAddressesNumber,
                        'randomExternalAddresses' => $randomExternalAddresses,
                        'externalAddresses' => $externalAddresses,
                        'privateAddressesNumber' => $privateAddressesNumber,
                        'randomPrivateAddresses' => $randomPrivateAddresses,
                        'privateAddresses' => $privateAddresses,
                    ]
                )->CreateNewVirtualMachineResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("CreateNewVirtualMachine Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function getVirtualMachineItem(int $itemId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'GetVirtualMachineItem',
                    [
                        'itemId' => $itemId
                    ])->GetVirtualMachineItemResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetVirtualMachineItem Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    public function getVirtualMachineGeneralDetails(int $itemId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'GetVirtualMachineGeneralDetails',
                    [
                        'itemId' => $itemId
                    ])->GetVirtualMachineGeneralDetailsResult); //symfony denormalize service?
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetVirtualMachineGeneralDetails Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    public function getExternalNetworkAdapterDetails(int $itemId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'GetExternalNetworkAdapterDetails',
                    [
                        'itemId' => $itemId
                    ])->GetExternalNetworkAdapterDetailsResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetExternalNetworkAdapterDetails Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }

    public function getVirtualMachines(int $packageId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    self::SERVICE,
                    'GetVirtualMachines',
                    [
                        'packageId' => $packageId,
                        'filterColumn' => '',
                        'filterValue' => '',
                        'sortColumn' => '',
                        'startRow' => '',
                        'maximumRows' => 100000000,
                        'recursive' => false
                    ])->GetVirtualMachinesResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetVirtualMachines Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
    }
}