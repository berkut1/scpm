<?php
declare(strict_types=1);

namespace App\DataFixtures\ControlPanel;

use App\Model\ControlPanel\Entity\Location\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class LocationFixture extends Fixture
{
    public const array REFERENCE_LOCATIONS = [
        'location_1',
        'location_2',
        'location_3',
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < count(self::REFERENCE_LOCATIONS); $i++) {
            $country = $faker->country();
            $location = new Location($country.$i);
            $manager->persist($location);
            $this->setReference(self::REFERENCE_LOCATIONS[$i], $location);
        }
        $manager->flush();
    }
}