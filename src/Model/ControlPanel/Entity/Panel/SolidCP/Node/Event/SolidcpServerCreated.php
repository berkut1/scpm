<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Node\Event;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;

final class SolidcpServerCreated
{
    public SolidcpServer $solidcpServer;

    public function __construct(SolidcpServer $solidcpServer)
    {
        $this->solidcpServer = $solidcpServer;
    }
}