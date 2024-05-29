<?php
declare(strict_types=1);

namespace App\DataFixtures\ControlPanel\Panel;

use App\DataFixtures\ControlPanel\LocationFixture;
use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class SolidcpServerFixture extends Fixture implements DependentFixtureInterface
{
    public const array REFERENCE_NODES = [
        'node_1',
        'node_2',
        'node_3',
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $max_node_num = count(EnterpriseDispatcherFixture::REFERENCE_ED);
        $max_loc_num = count(LocationFixture::REFERENCE_LOCATIONS);

        for ($i = 0; $i < count(self::REFERENCE_NODES); $i++) {
            /** @var EnterpriseDispatcher $enterpriseDispatcher */
            $rand_val = random_int(0, $max_node_num - 1);
            $enterpriseDispatcher = $this->getReference(EnterpriseDispatcherFixture::REFERENCE_ED[$rand_val]);

            /** @var Location $location */
            $rand_val = random_int(0, $max_loc_num - 1);
            $location = $this->getReference(LocationFixture::REFERENCE_LOCATIONS[$rand_val]);

            $core = $faker->numberBetween(8, 64);
            $node = new SolidcpServer(
                $enterpriseDispatcher,
                $location,
                $faker->name(),
                $core,
                ($core * 2),
                ($faker->numberBetween(1, 100) * 1024 * 1024),
            );

            $manager->persist($node);
            $this->setReference(self::REFERENCE_NODES[$i], $node);
        }
        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            LocationFixture::class,
            EnterpriseDispatcherFixture::class,
        ];
    }
}