<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\Package\PackageStatus;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

final class EsPackages extends SoapExecute
{
    public const string SERVICE = 'esPackages.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    /**
     * @throws \SoapFault
     */
    public function addPackageWithResources(
        int     $userId,
        int     $planId,
        string  $spaceName,
        int     $statusId = 1, //1 - Active
        bool    $sendLetter = false,
        bool    $createResources = true,
        string  $domainName = "",
        bool    $tempDomain = false,
        bool    $createWebSite = false,
        bool    $createFtpAccount = false,
        ?string $ftpAccountName = null,
        bool    $createMailAccount = false,
        string  $hostName = ""
    ): int
    {
        try {
            return $this->execute(
                self::SERVICE,
                'AddPackageWithResources',
                [
                    'userId' => $userId,
                    'planId' => $planId,
                    'spaceName' => $spaceName,
                    'statusId' => $statusId,
                    'sendLetter' => $sendLetter,
                    'createResources' => $createResources,
                    'domainName' => $domainName,
                    'tempDomain' => $tempDomain,
                    'createWebSite' => $createWebSite,
                    'createFtpAccount' => $createFtpAccount,
                    'ftpAccountName' => $ftpAccountName,
                    'createMailAccount' => $createMailAccount,
                    'hostName' => $hostName,
                ])->AddPackageWithResourcesResult->Result;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "AddPackageWithResources Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function changePackageStatus(int $packageId, PackageStatus $status): int
    {
        try {
            $result = $this->execute(
                self::SERVICE,
                'ChangePackageStatus',
                [
                    'packageId' => $packageId,
                    'status' => $status->getName(),
                ])->ChangePackageStatusResult;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "ChangePackageStatus Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }

        if ($result < 0) {
            throw new \DomainException('Fault: ' . "An unknown error occured (Code: {$result}). Please reference SolidCP BusinessErrorCodes for further information", $result);
        }
        return $result;
    }

    /**
     * @throws \SoapFault
     */
    public function updatePackageName(int $packageId, string $packageName, string $packageComments = null): int
    {
        try {
            $result = $this->execute(
                self::SERVICE,
                'UpdatePackageName',
                [
                    'packageId' => $packageId,
                    'packageName' => $packageName,
                    'packageComments' => $packageComments,
                ])->UpdatePackageNameResult;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "UpdatePackageName Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }

        if ($result < 0) {
            throw new \DomainException('Fault: ' . "An unknown error occured (Code: {$result}). Please reference SolidCP BusinessErrorCodes for further information", $result);
        }
        return $result;
    }

    /**
     * @throws \SoapFault
     */
    public function getUserPackages(int $userId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetMyPackages',
                ['userId' => $userId])->GetMyPackagesResult->PackageInfo);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetMyPackages Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getPackageAddons(int $packageId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetPackageAddons',
                ['packageId' => $packageId])->GetPackageAddonsResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetPackageAddons Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getHostingPlans(int $userId): array
    {
        try {
            $result = (array)$this->execute(
                self::SERVICE,
                'GetHostingPlans',
                [
                    'userId' => $userId,
                ])->GetHostingPlansResult;
            return $this->convertArray($result['any'], true);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetHostingPlans Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getHostingPlan(int $planId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetHostingPlan',
                ['planId' => $planId])->GetHostingPlanResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetPackageAddons Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getPackageContext(int $packageId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetPackageContext',
                ['packageId' => $packageId])->GetPackageContextResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetPackageContext Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    public function getPackageVPS2012StorageUsageGB(int $packageId): int
    {
        $packageContext = $this->getPackageContext($packageId);
        $packageQuotaValues = $packageContext['QuotasArray']['QuotaValueInfo'];
        $VPS2012hddQuotaId = 559; //SolidCP DB id value.
        $VPS2012hddQuotaIndex = array_search($VPS2012hddQuotaId, array_column($packageQuotaValues, 'QuotaId')); //search the index of array where the column has QuotaId = 559
        return $packageQuotaValues[$VPS2012hddQuotaIndex]['QuotaUsedValue'];
    }

    /**
     * @throws \SoapFault
     */
    public function getNestedPackagesSummary(int $packageId): array
    {
        try {
            $result = (array)$this->execute(
                self::SERVICE,
                'GetNestedPackagesSummary',
                ['packageId' => $packageId])->GetNestedPackagesSummaryResult;

            return $this->convertArray($result['any'], true);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetNestedPackagesSummary Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    public function getNumberOfActivePackages(int $packageId): int
    {
        $packageDataSet = $this->getNestedPackagesSummary($packageId);
        $countOfActivePackages = 0;

        if ($packageDataSet['NewDataSet']['Table']['PackagesNumber'] > 0) { //if we have packages, then get number of Active Packages
            $summary = $packageDataSet['NewDataSet']['Table1']; //in Table1 we have separated packages to Active/Suspended/Canceled
            /*thanks for this awful code - data return SolidCP*/
            if (isset($summary[0])) { //we can get different arrays from getNestedPackagesSummary
                foreach ($summary as $one) {
                    if ((int)$one['StatusID'] === 1) {
                        $countOfActivePackages = (int)$one['PackagesNumber'];
                        break;
                    }
                }
                unset($one);
            } else {
                if ((int)$summary['StatusID'] === 1) {
                    $countOfActivePackages = (int)$summary['PackagesNumber'];
                }
            }
        }

        return $countOfActivePackages;
    }

    /**
     * @throws \SoapFault
     */
    public function getPackageQuotas(int $packageId): array
    {
        try {
            $result = (array)$this->execute(
                self::SERVICE,
                'GetPackageQuotas',
                ['packageId' => $packageId])->GetPackageQuotasResult;

            return $this->convertArray($result['any'], true);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetPackageQuotas Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getRawPackages(int $userId): array
    {
        try {
            $result = (array)$this->execute(
                self::SERVICE,
                'GetRawPackages',
                ['userId' => $userId])->GetRawPackagesResult;

            return $this->convertArray($result['any'], true);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetRawPackages Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function getRawPackageItems(int $packageId): array
    {
        try {
            $result = (array)$this->execute(
                self::SERVICE,
                'GetRawPackageItems',
                ['packageId' => $packageId])->GetRawPackageItemsResult;

            return $this->convertArray($result['any'], true);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetRawPackageItems Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }
}