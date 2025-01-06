<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Locations;

use App\Model\ControlPanel\Entity\Location\Location;
use App\Tests\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LocationFixture extends Fixture
{
    public const int EXISTING_ID = 101;
    public const string REFERENCE = 'location';

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $exists = new Location('Exist Test Location');
        $this->setReference(self::REFERENCE, $exists);

        $reflection = new \ReflectionClass($exists);
        $property = $reflection->getProperty('id');
        $property->setValue($exists, self::EXISTING_ID);

        $manager->persist($exists);

        Utils::flushEntityWithCustomId($manager, Location::class);
        //$manager->flush();
    }
}