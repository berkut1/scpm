<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\SetDefault;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private EnterpriseDispatcherRepository $enterpriseDispatcherRepository;

    public function __construct(Flusher $flusher, EnterpriseDispatcherRepository $enterpriseDispatcherRepository)
    {
        $this->flusher = $flusher;
        $this->enterpriseDispatcherRepository = $enterpriseDispatcherRepository;
    }

    public function handle(Command $command): void
    {
        if($enterpriseDispatcherDefault = $this->enterpriseDispatcherRepository->getDefault()){
            $enterpriseDispatcherDefault->setNonDefault();
            //$this->flusher->flush();
        }

        $enterpriseDispatcher = $this->enterpriseDispatcherRepository->get($command->id);
        $enterpriseDispatcher->setDefault();

        $this->flusher->flush();
    }
}
