<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Manual;

class Command
{
    public string $login;
    public string $password;
    public string $role;

    public function __construct(string $login, string $password, string $role)
    {
        $this->login = $login;
        $this->password = $password;
        $this->role = $role;
    }
}
