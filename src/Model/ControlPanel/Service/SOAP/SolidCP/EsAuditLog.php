<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

final class EsAuditLog extends EnterpriseSoapServiceFactory
{
    protected const string SERVICE = 'esAuditLog.asmx';

    /**
     * @throws \SoapFault
     */
    public function getAuditLogRecord(string $taskId): array
    {
        try {
            return $this->convertArray($this->execute(
                'GetAuditLogRecord',
                ['taskId' => $taskId])->GetAuditLogRecord);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetAuditLogRecord Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }
}