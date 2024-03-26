<?php
declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private UserFetcher $users;
    private EntityManagerInterface $em;

    public function __construct(UserFetcher $users, EntityManagerInterface $em)
    {
        $this->users = $users;
        $this->em = $em;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Load a User object from your data source or throw UserNotFoundException.
        $user = $this->loadUser($identifier);
        return self::identityByUser($user, $identifier);
    }

    /**
     * @deprecated since Symfony 5.3, use loadUserByIdentifier() instead
     */
    public function loadUserByUsername($username) {}

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($user));
        }

        $loadUser = $this->loadUser($user->getUserIdentifier());
        return self::identityByUser($loadUser, $user->getUserIdentifier());
    }

    public function supportsClass($class): bool
    {
        //return $class instanceof UserIdentity;
        return UserIdentity::class === $class;
    }

    /**
     * Upgrades the encoded password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        // TODO: when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
        //dump($user);
        $entity = $this->users->get($user->getId());
        $entity->changePassword($newHashedPassword);
        $this->em->flush();
    }

    private function loadUser($username): AuthView
    {
        if ($user = $this->users->findForAuthByLogin($username)) {
            return $user;
        }

        throw new UserNotFoundException('');
    }

    private static function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->login ?: $username,
            $user->password ?: '',
            $user->role,
            $user->status
        );
    }
}
