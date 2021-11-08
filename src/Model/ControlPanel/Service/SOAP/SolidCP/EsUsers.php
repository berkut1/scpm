<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User\UserInfo;
use App\Model\ControlPanel\Service\NotFoundException;
use App\Model\ControlPanel\Service\SOAP\SoapExecute;

final class EsUsers extends SoapExecute
{
    public const SERVICE = 'esUsers.asmx';

    //private SoapExecute $soapExecute;

    public function __construct()
    {
        //parent::__construct();
        //$this->soapExecute = SoapExecute::initFromEnterpriseDispatcher($enterpriseDispatcher);
    }

    public static function createFromEnterpriseDispatcher(EnterpriseDispatcher $enterpriseDispatcher): self //TODO: move to a facade?
    {
        $soap = new self();
        $soap->initFromEnterpriseDispatcher($enterpriseDispatcher);
        return $soap;
    }

    public function addUser(UserInfo $userInfo, string $password, bool $sendLetter = false, ?string $notes = null): int
    {
        $result = $this->execute(self::SERVICE, 'AddUser', [
            'user' => $userInfo,
            'sendLetter' => $sendLetter,
            'password' => $password,
            'notes' => $notes,
        ])->AddUserResult; //return user id

        if ($result < 0) {
            throw new \DomainException('Fault: ' . Error::getFriendlyError($result), $result);
        }
        return $result;
    }

    public function getUserByUsername(string $username): array
    {
        try {
//            return $this->execute(self::SERVICE, 'GetUserByUsername', [
//                'username' => $username,
//            ])->GetUserByUsernameResult;
            $result = $this->convertArray($this->execute(
                self::SERVICE,
                'GetUserByUsername',
                ['username' => $username])->GetUserByUsernameResult);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("GetUserByUsername Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})", $e->getCode(), $e);
        }

        return $result;
    }

    public function userExists(string $username): bool
    {
        try {
            return $this->execute(self::SERVICE, 'UserExists', [
                'username' => $username
            ])->UserExistsResult;
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("UserExists Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function changeUserPassword(int $userId, string $password): int
    {
        try {
            $result = $this->execute(self::SERVICE, 'ChangeUserPassword', [
                'userId' => $userId,
                'password' => $password
            ])->ChangeUserPasswordResult;
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("ChangeUserPassword Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}", $e->getCode(), $e);
        }

        if ($result < 0) {
            throw new \DomainException('Fault: ' . Error::getFriendlyError($result), $result);
        }
        return $result;
    }

    public function updateUserLiteral(
        int    $userId,
        int    $roleId,
        int    $statusId,
        bool   $isPeer,
        bool   $isDemo,
        string $firstName,
        string $lastName,
        string $email,
        string $secondaryEmail,
        string $address,
        string $city,
        string $country,
        string $state,
        string $zip,
        string $primaryPhone,
        string $secondaryPhone,
        string $fax,
        string $instantMessenger,
        bool   $htmlMail,
        string $companyName,
        bool   $ecommerceEnabled

    ): int
    {
        try {
            $result = $this->execute(self::SERVICE, 'UpdateUserLiteral', [
                'userId' => $userId,
                'roleId' => $roleId,
                'statusId' => $statusId,
                'isPeer' => $isPeer,
                'isDemo' => $isDemo,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'secondaryEmail' => $secondaryEmail,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'state' => $state,
                'zip' => $zip,
                'primaryPhone' => $primaryPhone,
                'secondaryPhone' => $secondaryPhone,
                'fax' => $fax,
                'instantMessenger' => $instantMessenger,
                'htmlMail' => $htmlMail,
                'companyName' => $companyName,
                'ecommerceEnabled' => $ecommerceEnabled
            ])->UpdateUserLiteralResult;
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception("UpdateUserLiteral Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}", $e->getCode(), $e);
        }

        if ($result < 0) {
            throw new \DomainException('Fault: ' . Error::getFriendlyError($result), $result);
        }
        return $result;
    }
}