<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

final class EsTasks extends EnterpriseSoapServiceFactory
{
    protected const string SERVICE = 'esTasks.asmx';

    /**
     * @throws \SoapFault
     */
    public function getTask(string $taskId): array
    {
        try {
            return $this->convertArray($this->execute(
                'GetTask',
                ['taskId' => $taskId])->GetTaskResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetTask Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }
}