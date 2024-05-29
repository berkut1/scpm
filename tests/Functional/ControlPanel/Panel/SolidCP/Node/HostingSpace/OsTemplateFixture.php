<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplate;
use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Tests\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class OsTemplateFixture extends Fixture implements DependentFixtureInterface
{
    public const int EXISTING_ID = 10000001;
    public const string REFERENCE = 'template_os';

    public function load(ObjectManager $manager): void
    {
        /** @var SolidcpHostingSpace $hostingSpace */
        $hostingSpace = $this->getReference(HostingSpaceFixture::REFERENCES[HostingSpaceFixture::EXISTING_ID_ENABLED]);

        $osTemplate = new OsTemplate($hostingSpace, 'exist_os_file.vhdx', 'Exist OS File');

        $reflection = new \ReflectionClass($osTemplate);
        $property = $reflection->getProperty('id');
        $property->setValue($osTemplate, self::EXISTING_ID);

        $manager->persist($osTemplate);
        $this->setReference(self::REFERENCE, $osTemplate);

        Utils::flushEntityWithCustomId($manager, OsTemplate::class);
    }

    public function getDependencies(): array
    {
        return [
            HostingSpaceFixture::class,
        ];
    }
}