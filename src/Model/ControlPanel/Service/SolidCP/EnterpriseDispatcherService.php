<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SolidCP;

use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsersInterface;

final readonly class EnterpriseDispatcherService
{
    public function __construct(
        private EsUsersInterface $esUsers
    ) {}

    public function getEnterpriseDispatcherRealUserId(string $url, string $login, string $password): int
    {
        $this->esUsers->initManual($url, $login, $password);
        try {
            $result = $this->esUsers->getUserByUsername($login);
        } catch (\Exception $e) {
            throw new \DomainException("Soap execution error (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }
        if ($result['IsPeer']) {
            throw new \DomainException("This Login {$login} is Peer. Please use a real User, not Peer");
        }
        return (int)$result['UserId'];
    }
}