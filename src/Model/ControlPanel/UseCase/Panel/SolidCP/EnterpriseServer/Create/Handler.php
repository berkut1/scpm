<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Create;

use App\Model\ControlPanel\Service\SOAP\SoapExecute;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\ControlPanel\Service\SOAP\SolidCP\EsUsers;
use App\Model\ControlPanel\Service\SolidCP\EnterpriseServerService;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseServerRepository $enterpriseServerRepository;
    private EnterpriseServerService $enterpriseServerService;

    public function __construct(EnterpriseServerRepository $enterpriseServerRepository, Flusher $flusher, EnterpriseServerService $enterpriseServerService)
    {
        $this->flusher = $flusher;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
        $this->enterpriseServerService = $enterpriseServerService;
    }

    public function handle(Command $command): void
    {
        if ($this->enterpriseServerRepository->hasByName($command->name)) {
            throw new \DomainException('EnterpriseServer with this name already exists.');
        }
        $headers = @get_headers($command->url);
        if (!($headers && strpos($headers[0], '200'))) {
            throw new \DomainException('EnterpriseServer is unreachable.');
        }


        //$enterpriseServerWithFakeUserId = new EnterpriseServer($command->name, $command->url, $command->login, $command->password, 0); //only for getting esUser resources.
        //$esUsers = EsUsers::createFromEnterpriseServer($enterpriseServerWithFakeUserId);
        //$result = $esUsers->getUserByUsername($command->login);
        //$userId = (int)$result['UserId'];
        $enterpriseServer = new EnterpriseServer($this->enterpriseServerService, $command->name, $command->url, $command->login, $command->password);
        if($this->enterpriseServerRepository->getDefaultOrNull() === null){
            $enterpriseServer->setDefault();
        }

//        $rrr = EsPackages::createFromEnterpriseServer($enterpriseServer);
//        $rrr2 = [];
//        for ($i = 1; $i <= 400; $i++) {
//
//            //$rrr2[] = $rrr->getHostingPlans($userId)['NewDataSet']['Table'];
//            //$rrr3[] = $this->hostingSpaceService->allNotAddedHostingSpacesFrom(1);
//        }
//
//        dump($rrr2);

//        throw new \DomainException('EnterpriseServer is unreachable.');
        $this->enterpriseServerRepository->add($enterpriseServer);
        $this->flusher->flush($enterpriseServer);
    }
}