<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\User\Entity\User\UserRepository;
use App\Service\CustomHttpClient;
use App\Tests\Builder\User\UserMapper;
use App\Tests\Mock\CustomHttpClientMock;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();
        //We use https://github.com/dmaicher/doctrine-test-bundle
        //If we do manually, it gets some problem with mocking classes in tests
        /*$this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);*/
    }

    #[\Override]
    protected function tearDown(): void
    {
        /*$this->em->getConnection()->rollback();
        $this->em->close();*/
        parent::tearDown();
    }

    /**
     * Logs in as a user with the specified name.
     *
     * This method should only be called after mocking the necessary classes.
     * Calling it before mocking may result in errors. (service is already initialized, you cannot replace it.)
     */
    protected function loginAs(string $name): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->getByLogin($name);
        $this->client->loginUser(UserMapper::mapUserToUserIdentity($user));
    }

    protected function apiLoginAs(string $username, string $password = 'password'): void
    {
        $this->client->request(
            'POST',
            '/api/login/authentication_token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    protected function setCustomHttpClientRespond(string $url, array $willRespond): void
    {
        $httpClientMock = $this->client->getContainer()->get(CustomHttpClient::class);
        if ($httpClientMock === null) {
            $httpClientMock = new CustomHttpClientMock();
            $this->client->getContainer()->set(CustomHttpClient::class, $httpClientMock);
        }
        $httpClientMock->addResponse($url, $willRespond);
    }
}