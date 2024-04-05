<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\AuditLog\Entity\AuditLog;
use App\Model\AuditLog\Entity\Entity;
use App\Model\AuditLog\Entity\Id;
use App\Model\AuditLog\Entity\Record\Record;
use App\Model\User\Entity\AuditLog\EntityType;
use App\Model\User\Entity\AuditLog\TaskName;
use App\Model\User\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class AuditLogFixture extends Fixture implements DependentFixtureInterface
{
    public const array REFERENCE_LOGS = [
        'log_1',
        'log_2',
        'log_3',
        'log_4',
        'log_5',
        'log_6',
        'log_7',
        'log_8',
        'log_9',
        'log_10',
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $max_user_num = count(UserFixture::REFERENCE_USERS);

        for ($i = 0; $i < count(self::REFERENCE_LOGS); $i++) {
            /** @var User $user */
            $rand_val = random_int(0, $max_user_num - 1);
            $user = $this->getReference(UserFixture::REFERENCE_USERS[$rand_val]);
            $ipv4 = $faker->ipv4();
            $date = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween());

            $entity = new Entity(EntityType::userUser(), $user->getId()->getValue());
            $log = AuditLog::createAsSystem(Id::next(),
                $ipv4, $entity, TaskName::loginUser(), [
                    Record::create('LOGIN_USER_FROM_IP', [
                        $user->getId(),
                        $ipv4,
                    ]),
                ])->withCustomTime($date);

            $manager->persist($log);
            $this->setReference(self::REFERENCE_LOGS[$i], $log);
        }
        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}