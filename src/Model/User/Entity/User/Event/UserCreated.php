<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User\Event;

use App\Model\User\Entity\User\Id;

class UserCreated
{
    public Id $id_createdUser;

    public function __construct(Id $id_createdUser)
    {
        $this->id_createdUser = $id_createdUser;
    }
}