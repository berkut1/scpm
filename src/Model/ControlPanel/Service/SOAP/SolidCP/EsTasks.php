<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\NotFoundException;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

class EsTasks extends SoapExecute
{
    public const SERVICE = 'esTasks.asmx';

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    public function getTask(string $taskId): array
    {
        try {
            $result = $this->convertArray($this->execute(
                self::SERVICE,
                'GetTask',
                ['taskId' => $taskId])->GetTaskResult);

            return $result;
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetTask Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}", $e->getCode(), $e);
        }
    }
}