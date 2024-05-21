<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Tests\Builder\ControlPanel\Panel\SolidcpHostingSpaceBuilder;
use App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\NodeFixture;
use App\Tests\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class HostingSpaceFixture extends Fixture implements DependentFixtureInterface
{
    public const int EXISTING_ID_ENABLED = 100001;
    public const int EXISTING_ID_DISABLED = 100002;
    public const array REFERENCES = [
        self::EXISTING_ID_ENABLED => 'hs_1',
        self::EXISTING_ID_DISABLED => 'hs_2',
    ];

    public function load(ObjectManager $manager): void
    {
        $server = $this->getReference(NodeFixture::REFERENCES[NodeFixture::EXISTING_ID_ENABLED]);

        $hostingSpace = (new SolidcpHostingSpaceBuilder($server, random_int(100, 999)))
            ->withDetails('Exist Hosting Space Enabled', 40, 24 * 1024 * 1024, 500)
            ->withId(self::EXISTING_ID_ENABLED)
            ->build();
        $manager->persist($hostingSpace);
        $this->setReference(self::REFERENCES[self::EXISTING_ID_ENABLED], $hostingSpace);

        $hostingSpace = (new SolidcpHostingSpaceBuilder($server, random_int(1, 99), false))
            ->withDetails('Exist Hosting Space Disabled', 20, 28 * 1024 * 1024, 300)
            ->withId(self::EXISTING_ID_DISABLED)
            ->build();
        $manager->persist($hostingSpace);
        $this->setReference(self::REFERENCES[self::EXISTING_ID_DISABLED], $hostingSpace);

        Utils::flushEntityWithCustomId($manager, SolidcpHostingSpace::class);
    }

    public function getDependencies(): array
    {
        return [
            NodeFixture::class,
        ];
    }
}