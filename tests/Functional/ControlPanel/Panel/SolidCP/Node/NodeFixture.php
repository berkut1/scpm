<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;
use App\Tests\Functional\ControlPanel\Locations\LocationFixture;
use App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFixture;
use App\Tests\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NodeFixture extends Fixture implements DependentFixtureInterface
{
    public const int EXISTING_ID_ENABLED = 10001;
    public const int EXISTING_ID_DISABLED = 10002;
    public const array REFERENCES = [
        self::EXISTING_ID_ENABLED => 'server_1',
        self::EXISTING_ID_DISABLED => 'server_2',
    ];


    public function load(ObjectManager $manager): void
    {
        $enterpriseDispatcher = $this->getReference(EnterpriseDispatcherFixture::REFERENCES[EnterpriseDispatcherFixture::EXISTING_ID_ENABLED]);
        $location = $this->getReference(LocationFixture::EXISTING_ID);

        $server = (new SolidCPServerBuilder($enterpriseDispatcher))
            ->withServerDetails('Exist Node Enabled', 12, 24, 1024 * 46)
            ->withLocation($location)
            ->withId(self::EXISTING_ID_ENABLED)
            ->build();
        $manager->persist($server);
        $this->setReference(self::REFERENCES[self::EXISTING_ID_ENABLED], $server);

        $server = (new SolidCPServerBuilder($enterpriseDispatcher, false))
            ->withServerDetails('Exist Node Disabled', 14, 28, 1024 * 42)
            ->withLocation($location)
            ->withId(self::EXISTING_ID_DISABLED)
            ->build();
        $manager->persist($server);
        $this->setReference(self::REFERENCES[self::EXISTING_ID_DISABLED], $server);

        Utils::flushEntityWithCustomId($manager, SolidcpServer::class);
    }

    public function getDependencies(): array
    {
        return [
            LocationFixture::class,
            EnterpriseDispatcherFixture::class,
        ];
    }
}