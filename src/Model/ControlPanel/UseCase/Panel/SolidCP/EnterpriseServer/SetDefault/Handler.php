<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\SetDefault;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServerRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseServerRepository $enterpriseServerRepository;

    public function __construct(Flusher $flusher, EnterpriseServerRepository $enterpriseServerRepository)
    {
        $this->flusher = $flusher;
        $this->enterpriseServerRepository = $enterpriseServerRepository;
    }

    public function handle(Command $command): void
    {
        if($enterpriseServerDefault = $this->enterpriseServerRepository->getDefault()){
            $enterpriseServerDefault->setNonDefault();
            //$this->flusher->flush();
        }

        $enterpriseServer = $this->enterpriseServerRepository->get($command->id);
        $enterpriseServer->setDefault();

        $this->flusher->flush();
    }
}
