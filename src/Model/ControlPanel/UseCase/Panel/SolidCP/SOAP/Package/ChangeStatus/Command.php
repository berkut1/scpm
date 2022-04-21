<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\ChangeStatus;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public ?int $id_enterprise_dispatcher;

    #[Assert\NotBlank]
    public int $solidcp_package_id;

    #[Assert\NotBlank]
    public string $solidcp_package_status;

    public function __construct(int $solidcp_package_id, string $solidcp_package_status, ?int $id_enterprise_dispatcher = null)
    {
        $this->solidcp_package_id = $solidcp_package_id;
        $this->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $this->solidcp_package_status = $solidcp_package_status;
    }
}
