<?php
declare(strict_types=1);

namespace App\Tests\Functional\Location;

use App\Model\ControlPanel\Entity\Location\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LocationFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $exists = new Location('Exist Test Location');
        $manager->persist($exists);
        $manager->flush();
    }
}