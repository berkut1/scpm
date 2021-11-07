<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;

class EnterpriseServerService
{
    private EsUsers $esUsers;

    public function __construct(EsUsers $esUsers)
    {
        $this->esUsers = $esUsers;
    }

    public function getEnterpriseServerRealUserId(string $url, string $login, string $password): int
    {
        $this->esUsers->initManual($url, $login, $password);
        try {
            $result = $this->esUsers->getUserByUsername($login);
        } catch (\Exception $e) {
            throw new \DomainException("Soap execution error (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
        if($result['IsPeer']){
            throw new \DomainException("This Login {$login} is Peer. Please use a real User, not Peer");
        }
        return (int)$result['UserId'];
    }
}