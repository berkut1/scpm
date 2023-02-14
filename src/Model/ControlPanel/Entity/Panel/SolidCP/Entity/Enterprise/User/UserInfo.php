<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Enterprise\User;

class UserInfo
{
    public int $UserId = 0;
    public int $OwnerId;
    public int $RoleId;
    public UserRole $Role;
    public int $StatusId;
    public UserStatus $Status;
    public int $LoginStatusId;
    public UserLoginStatus $LoginStatus;
    public int $FailedLogins = 0;
    public string $Created;
    public string $Changed;
    public bool $IsPeer;
    public bool $IsDemo;
    public string $Comments;
    public string $Username;
    public string $FirstName;
    public string $LastName;
    public string $Email;
    public string $SecondaryEmail = '';
    public string $Address = '';
    public string $City = '';
    public string $Country = '';
    public string $State = '';
    public string $Zip = '';
    public string $PrimaryPhone = '';
    public string $SecondaryPhone = '';
    public string $Fax = '';
    public string $InstantMessenger = '';
    public bool $HtmlMail;
    public string $CompanyName = '';
    public bool $EcommerceEnabled = false;
    public string $SubscriberNumber = '';
    public int $MfaMode = 0;


    public function __construct(int $ownerId, UserRole $role, UserStatus $status, bool $isPeer, bool $isDemo, string $username, string $firstName, string $lastName, string $email, bool $htmlMail)
    {
        $this->OwnerId = $ownerId;
        $this->RoleId = $role->getId();
        $this->Role = UserRole::user();
        $this->StatusId = $status->getId();
        $this->Status = $status;
        $this->IsPeer = $isPeer;
        $this->IsDemo = $isDemo;
        $this->Username = $username;
        $this->FirstName = $firstName;
        $this->LastName = $lastName;
        $this->Email = $email;
        $this->HtmlMail = $htmlMail;
        $this->LoginStatusId = UserLoginStatus::default()->getId();
        $this->LoginStatus = UserLoginStatus::default();
        $this->Created = (new \DateTime("now"))->format("Y-m-d\TH:i:s.u");
        $this->Changed = (new \DateTime("now"))->format("Y-m-d\TH:i:s.u");
    }
}