<?php
declare(strict_types=1);

namespace App\ReadModel\User;

use App\ReadModel\CustomObjectInterface;
use App\ReadModel\FromArrayTrait;

final class AuthView implements CustomObjectInterface
{
    use FromArrayTrait;

    public string $id = '';
    public string $login = '';
    public string $password = '';
    public string $role = '';
    public string $status = '';
}
