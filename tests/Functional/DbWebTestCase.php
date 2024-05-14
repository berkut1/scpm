<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\User\Entity\User\UserRepository;
use App\Service\CustomHttpClient;
use App\Tests\Builder\User\UserMapper;
use App\Tests\Mock\CustomHttpClientMock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbWebTestCase extends WebTestCase
{
    private EntityManagerInterface $em;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollback();
        $this->em->close();
        parent::tearDown();
    }

    protected function loginAs(string $name): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin($name);
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));
    }
}