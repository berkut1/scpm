<?php
declare(strict_types=1);

namespace App\DataFixtures\ControlPanel\Panel;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class EnterpriseDispatcherFixture extends Fixture
{
    public function __construct() {
        \DG\BypassFinals::enable(); //We can put in bin/console.php before kernel load, but that will enable it for all commands, so it's probably not a good idea.
    }

    public const array REFERENCE_ED = [
        'EnterpriseDispatcher_1',
        'EnterpriseDispatcher_2',
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < count(self::REFERENCE_ED); $i++) {
            $url = $faker->url();
            $test_login = $faker->domainName();
            $password = $faker->password();
            $esUsersMock = $this->mockEsUsersWith(
                $url,
                $test_login,
                random_int(1, 100),
                $password);

            $enterpriseDispatcherService = new EnterpriseDispatcherService($esUsersMock);
            $enterpriseDispatcher = new EnterpriseDispatcher(
                $enterpriseDispatcherService,
                $faker->name(),
                $url,
                $test_login,
                $password);

            $manager->persist($enterpriseDispatcher);
            $this->setReference(self::REFERENCE_ED[$i], $enterpriseDispatcher);
        }
        $manager->flush();
    }

    private function mockEsUsersWith($url, $login, $UserId, $password): EsUsers
    {
        $esUsersMock = \Mockery::mock(EsUsers::class);
        $esUsersMock->shouldReceive('initManual')->with($url, $login, $password);
        $esUsersMock->shouldReceive('getUserByUsername')->with($login)->andReturn(['UserId' => $UserId, 'IsPeer' => false]);
        return $esUsersMock;
    }

    public function __destruct()
    {
        \Mockery::close();
    }
}