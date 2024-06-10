<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Packages\VirtualMachines;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class VmPackageFixture extends Fixture
{
    public const string EXISTING_ID = '018f6ee9-2b3e-72e4-9aa2-eb61ec04b2d1';

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $exists = new VirtualMachinePackage(
            new Id(self::EXISTING_ID),
            'Exist Test VM Package RDP23',
            2,
            2,
            3072,
            80,
            0,
            0
        );
        $manager->persist($exists);
        $manager->flush();
    }
}