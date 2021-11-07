<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Entity\Server;


class ServiceProviderItem
{
    public int $Id = 0;
    public int $TypeId = 0;
    public int $PackageId = -1;
    public int $ServiceId = -1;
    public ?string $Name;
    public array $properties = [];
    public ?string $groupName = null;
    public string $CreatedDate;

    public function __construct()
    {
        $this->CreatedDate = (new \DateTime("now"))->format("Y-m-d\TH:i:s.u");
    }

}