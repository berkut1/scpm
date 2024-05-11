<?php
declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\User;
use App\Security\UserIdentity;

final class UserMapper
{
    public static function mapUserToUserIdentity(User $user): UserIdentity
    {
        $reflectionUser = new \ReflectionClass($user);
        $reflectionUserIdentity = new \ReflectionClass(UserIdentity::class);

        $userIdentityProperties = $reflectionUserIdentity->getProperties();

        $mappedProperties = [];

        foreach ($userIdentityProperties as $property) {
            $propertyName = $property->getName();
            $propertyName = $propertyName === 'username' ? 'login' : $propertyName;
            $userProperty = $reflectionUser->getProperty($propertyName);

            $mappedProperties[$propertyName] = $userProperty->getValue($user);
        }

        return new UserIdentity(
            (string)$mappedProperties['id'],
            $mappedProperties['login'],
            $mappedProperties['password'],
            (string)$mappedProperties['role'],
            (string)$mappedProperties['status']
        );
    }
}