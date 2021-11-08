<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Create;

use App\Model\ControlPanel\Service\SOAP\SoapExecute;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseDispatcherService;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;
    private EnterpriseDispatcherService $enterpriseDispatcherService;

    public function __construct(EnterpriseDispatcherRepository $enterpriseDispatcherRepository, Flusher $flusher, EnterpriseDispatcherService $enterpriseDispatcherService)
    {
        $this->flusher = $flusher;
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
        $this->enterpriseDispatcherService = $enterpriseDispatcherService;
    }

    public function handle(Command $command): void
    {
        if ($this->enterpriseDispatcherRepository->hasByName($command->name)) {
            throw new \DomainException('EnterpriseDispatcher with this name already exists.');
        }
        $headers = @get_headers($command->url);
        if (!($headers && strpos($headers[0], '200'))) {
            throw new \DomainException('EnterpriseDispatcher is unreachable.');
        }


        //$enterpriseDispatcherWithFakeUserId = new EnterpriseDispatcher($command->name, $command->url, $command->login, $command->password, 0); //only for getting esUser resources.
        //$esUsers = EsUsers::createFromEnterpriseDispatcher($enterpriseDispatcherWithFakeUserId);
        //$result = $esUsers->getUserByUsername($command->login);
        //$userId = (int)$result['UserId'];
        $enterpriseDispatcher = new EnterpriseDispatcher($this->enterpriseDispatcherService, $command->name, $command->url, $command->login, $command->password);
        if($this->enterpriseDispatcherRepository->getDefaultOrNull() === null){
            $enterpriseDispatcher->setDefault();
        }

//        $rrr = EsPackages::createFromEnterpriseDispatcher($enterpriseDispatcher);
//        $rrr2 = [];
//        for ($i = 1; $i <= 400; $i++) {
//
//            //$rrr2[] = $rrr->getHostingPlans($userId)['NewDataSet']['Table'];
//            //$rrr3[] = $this->hostingSpaceService->allNotAddedHostingSpacesFrom(1);
//        }
//
//        dump($rrr2);

//        throw new \DomainException('EnterpriseDispatcher is unreachable.');
        $this->enterpriseDispatcherRepository->add($enterpriseDispatcher);
        $this->flusher->flush($enterpriseDispatcher);
    }
}