<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\NotFoundException;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

class EsAuditLog extends SoapExecute
{
    public const SERVICE = 'esAuditLog.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    public function getAuditLogRecord(string $taskId): array
    {
        try {
            $result = $this->convertArray($this->execute(
                self::SERVICE,
                'GetAuditLogRecord',
                ['taskId' => $taskId])->GetAuditLogRecord);

            return $result;
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetAuditLogRecord Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}", $e->getCode(), $e);
        }
    }
}