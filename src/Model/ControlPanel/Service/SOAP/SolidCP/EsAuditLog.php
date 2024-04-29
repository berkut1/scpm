<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

final class EsAuditLog extends SoapExecute
{
    public const string SERVICE = 'esAuditLog.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    /**
     * @throws \SoapFault
     */
    public function getAuditLogRecord(string $taskId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetAuditLogRecord',
                ['taskId' => $taskId])->GetAuditLogRecord);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetAuditLogRecord Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }
}