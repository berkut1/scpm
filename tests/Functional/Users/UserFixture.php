<?php
declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Model\ControlPanel\Entity\Location\Location;
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
    public const string EXISTING_ID = '00000000-0000-0000-0000-000000000001';

    public const array REFERENCE_USERS = [
        'test_user_1',
        'test_user_2',
    ];
    public function __construct(private readonly PasswordHasher $hasher) {}
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $textPassword = 'password';
        $admin = $this->createUser('test_admin', $textPassword, Id::next());
        $admin->changeRole(Role::admin());
        $manager->persist($admin);
        $this->setReference(self::REFERENCE_USERS[0], $admin);

        $user = $this->createUser('test_user', $textPassword, new Id(self::EXISTING_ID));
        $manager->persist($user);
        $this->setReference(self::REFERENCE_USERS[1], $user);

        $manager->flush();
    }

    private function createUser(string $login, string $textPassword, Id $id): User
    {
        $userIdentity = new UserIdentity(
            $id->getValue(),
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