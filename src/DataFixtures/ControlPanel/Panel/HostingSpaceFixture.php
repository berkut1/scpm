<?php
declare(strict_types=1);

namespace App\DataFixtures\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class HostingSpaceFixture extends Fixture implements DependentFixtureInterface
{
    public const array REFERENCES = [
        'hostingSpace_1',
        'hostingSpace_2',
        'hostingSpace_3',
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $max_num = count(SolidcpServerFixture::REFERENCE_NODES);

        for ($i = 0; $i < count(self::REFERENCES); $i++) {
            /** @var SolidcpServer $solidcpServer */
            $rand_val = random_int(0, $max_num - 1);
            $solidcpServer = $this->getReference(SolidcpServerFixture::REFERENCE_NODES[$rand_val]);

            $solidcpHostingSpace = new SolidcpHostingSpace(
                $solidcpServer,
                $faker->numberBetween(1, 99999),
                $faker->name(),
                $faker->numberBetween(8, 64),
                $faker->numberBetween(64, 512) * 1024 * 1024,
                $faker->numberBetween(500, 1000),
            );

            $manager->persist($solidcpHostingSpace);
            $this->setReference(self::REFERENCES[$i], $solidcpHostingSpace);
        }
        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            SolidcpServerFixture::class,
        ];
    }
}