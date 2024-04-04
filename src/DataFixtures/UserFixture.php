<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\Status;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use App\Security\UserIdentity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends Fixture
{
    public function __construct(private readonly PasswordHasher $hasher) {}

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $textPassword = 'password';
        $admin = $this->createUser('berkut', $textPassword);
        $admin->changeRole(Role::admin());
        $manager->persist($admin);

        $user = $this->createUser('user', $textPassword);
        $manager->persist($user);

        $manager->flush();
    }


    private function createUser(string $login, string $textPassword): User
    {
        $userIdentity = new UserIdentity(
            Id::next()->getValue(),
            $login,
            '',
            Role::user()->getName(),
            Status::active()->getName()
        );
        $hash = $this->hasher->hash($userIdentity, $textPassword);
        return User::create(
            new Id($userIdentity->getId()),
            new \DateTimeImmutable(),
            $userIdentity->getUserIdentifier(),
            $hash
        );
    }
}
