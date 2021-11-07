<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    private PasswordHasher $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $admin = $this->createUser('berkut', $hash);
        $admin->changeRole(Role::admin());
        $manager->persist($admin);

        $user = $this->createUser('user', $hash);
        $manager->persist($user);

        $manager->flush();
    }


    private function createUser(string $login, string $hash): User
    {
        return User::create(
            Id::next(),
            new \DateTimeImmutable(),
            $login,
            $hash
        );
    }
}
