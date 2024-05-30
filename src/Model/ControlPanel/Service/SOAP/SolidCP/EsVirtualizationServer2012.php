<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\VirtualizationServer2012\VirtualMachineRequestedState;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server\VirtualMachine\VirtualMachine;

final class EsVirtualizationServer2012 extends EnterpriseSoapServiceFactory
{
    protected const string SERVICE = 'esVirtualizationServer2012.asmx';

    /**
     * @throws \SoapFault
     */
    public function generateMacAddress(): string
    {
        try {
            return $this->execute(
                'GenerateMacAddress', [])->GenerateMacAddressResult;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GenerateMacAddress Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function changeVirtualMachineState(int $itemId, VirtualMachineRequestedState $state): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    'ChangeVirtualMachineState', [
                    'itemId' => $itemId,
                    'state' => $state,
                ])->ChangeVirtualMachineStateResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "ChangeVirtualMachineState Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getOperatingSystemTemplates(int $packageId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    'GetOperatingSystemTemplates',
                    [
                        'packageId' => $packageId,
                    ])->GetOperatingSystemTemplatesResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetOperatingSystemTemplates Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function createNewVirtualMachine(
        VirtualMachine $vmSettings,
        string         $osTemplateFile,
        string         $password,
        int            $externalAddressesNumber = 0,
        bool           $randomExternalAddresses = false,
        array          $externalAddresses = [],
        int            $privateAddressesNumber = 0,
        bool           $randomPrivateAddresses = false,
        array          $privateAddresses = [],
        ?string        $summaryLetterEmail = null
    ): array
    {
        try {
            return $this->convertArray(
                $this->execute(
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
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "CreateNewVirtualMachine Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getVirtualMachineItem(int $itemId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    'GetVirtualMachineItem',
                    [
                        'itemId' => $itemId,
                    ])->GetVirtualMachineItemResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetVirtualMachineItem Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getVirtualMachineGeneralDetails(int $itemId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    'GetVirtualMachineGeneralDetails',
                    [
                        'itemId' => $itemId,
                    ])->GetVirtualMachineGeneralDetailsResult); //symfony denormalize service?
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetVirtualMachineGeneralDetails Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getExternalNetworkAdapterDetails(int $itemId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    'GetExternalNetworkAdapterDetails',
                    [
                        'itemId' => $itemId,
                    ])->GetExternalNetworkAdapterDetailsResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetExternalNetworkAdapterDetails Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getVirtualMachines(int $packageId): array
    {
        try {
            return $this->convertArray(
                $this->execute(
                    'GetVirtualMachines',
                    [
                        'packageId' => $packageId,
                        'filterColumn' => '',
                        'filterValue' => '',
                        'sortColumn' => '',
                        'startRow' => '',
                        'maximumRows' => 100000000,
                        'recursive' => false,
                    ])->GetVirtualMachinesResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetVirtualMachines Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }
    }
}