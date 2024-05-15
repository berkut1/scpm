<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Tests\Builder\ControlPanel\Panel\SolidcpServerBuilder;

trait TestSolidcpServerSetupTrait
{
    use TestEnterpriseDispatcherSetupTrait {
        TestEnterpriseDispatcherSetupTrait::setUp as protected setUpEnterpriseDispatcher;
    }

    protected SolidcpServer $solidcpServer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpEnterpriseDispatcher();
        $this->solidcpServer = (new SolidcpServerBuilder($this->enterpriseDispatcher))->build();
    }
}