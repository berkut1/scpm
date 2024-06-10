<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User\UserInfo;

final class EsUsers extends EnterpriseSoapServiceFactory
{
    protected const string SERVICE = 'esUsers.asmx';

    /**
     * @throws \SoapFault
     */
    public function addUser(UserInfo $userInfo, string $password, bool $sendLetter = false, ?string $notes = null): int
    {
        $result = $this->execute('AddUser', [
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

    /**
     * @throws \SoapFault
     */
    public function getUserByUsername(string $username): array
    {
        try {
//            return $this->execute('GetUserByUsername', [
//                'username' => $username,
//            ])->GetUserByUsernameResult;
            $result = $this->convertArray($this->execute(
                'GetUserByUsername',
                ['username' => $username])->GetUserByUsernameResult);
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "GetUserByUsername Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()})");
        }

        return $result;
    }

    /**
     * @throws \SoapFault
     */
    public function userExists(string $username): bool
    {
        try {
            return $this->execute('UserExists', [
                'username' => $username,
            ])->UserExistsResult;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "UserExists Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }
    }

    /**
     * @throws \SoapFault
     */
    public function changeUserPassword(int $userId, string $password): int
    {
        try {
            $result = $this->execute('ChangeUserPassword', [
                'userId' => $userId,
                'password' => $password,
            ])->ChangeUserPasswordResult;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "ChangeUserPassword Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }

        if ($result < 0) {
            throw new \DomainException('Fault: ' . Error::getFriendlyError($result), $result);
        }
        return $result;
    }

    /**
     * @throws \SoapFault
     */
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
            $result = $this->execute('UpdateUserLiteral', [
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
                'ecommerceEnabled' => $ecommerceEnabled,
            ])->UpdateUserLiteralResult;
        } catch (\SoapFault $e) {
            throw new \SoapFault($e->faultcode, "UpdateUserLiteral Fault: (Code: {$e->getCode()}, Message: {$e->getMessage()}");
        }

        if ($result < 0) {
            throw new \DomainException('Fault: ' . Error::getFriendlyError($result), $result);
        }
        return $result;
    }
}