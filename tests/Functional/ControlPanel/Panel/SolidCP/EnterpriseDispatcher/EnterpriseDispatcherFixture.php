<?php
declare(strict_types=1);

namespace App\Tests\Functional\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Tests\Builder\ControlPanel\Panel\EnterpriseDispatcherBuilder;
use App\Tests\Utils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EnterpriseDispatcherFixture extends Fixture
{
    public const int EXISTING_ID_ENABLED = 1001;
    public const int EXISTING_ID_DISABLED = 1002;
    public const array REFERENCES = [
        self::EXISTING_ID_ENABLED => 'enterprise_dispatcher_1',
        self::EXISTING_ID_DISABLED => 'enterprise_dispatcher_2',
    ];


    public function __construct()
    {
        //if renew a cache, then need to run double time fixture (first time will be exception)
        \DG\BypassFinals::setCacheDirectory('\var\cache');
        \DG\BypassFinals::enable(); //We can put in bin/console.php before kernel load, but that will enable it for all commands, so it's probably not a good idea.
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {

        $esUsersMock = $this->mockEsUsersWith(
            $url = 'http://10.0.0.1:9002',
            $login = 'test_es_login',
            111,
            $password = 'password');

        $enterpriseDispatcherService = new EnterpriseDispatcherService($esUsersMock);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($enterpriseDispatcherService))
            ->via('Exist Test Enterprise Enabled', $url, $login, $password)
            ->withId(self::EXISTING_ID_ENABLED)
            ->build();

        $manager->persist($enterpriseDispatcher);
        $this->setReference(self::REFERENCES[self::EXISTING_ID_ENABLED], $enterpriseDispatcher);

        $esUsersMock = $this->mockEsUsersWith(
            $url = 'http://10.0.0.2:9002',
            $login = 'test_es_login2',
            112,
            $password = 'password2');

        $enterpriseDispatcherService = new EnterpriseDispatcherService($esUsersMock);
        $enterpriseDispatcher = (new EnterpriseDispatcherBuilder($enterpriseDispatcherService))
            ->via('Exist Test Enterprise Disabled', $url, $login, $password)
            ->withId(self::EXISTING_ID_DISABLED)
            ->byDefaultDisabled()
            ->build();


        $this->setReference(self::REFERENCES[self::EXISTING_ID_DISABLED], $enterpriseDispatcher);
        $manager->persist($enterpriseDispatcher);

        Utils::flushEntityWithCustomId($manager, EnterpriseDispatcher::class);
//        $manager->flush();
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