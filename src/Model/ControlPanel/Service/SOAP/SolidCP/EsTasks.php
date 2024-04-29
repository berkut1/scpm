<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

final class EsTasks extends SoapExecute
{
    public const string SERVICE = 'esTasks.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    /**
     * @throws \SoapFault
     */
    public function getTask(string $taskId): array
    {
        try {
            return $this->convertArray($this->execute(
                self::SERVICE,
                'GetTask',
                ['taskId' => $taskId])->GetTaskResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetTask Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }
}