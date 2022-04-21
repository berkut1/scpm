<?php

declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\AvailableSpacePlan;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $server_location_name = '';

    #[Assert\NotBlank]
    public string $server_package_name = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $server_ip_amount = 0;
    /** @var int[] */
    private array $ignore_node_ids = [];
    /** @var int[] */
    private array $ignore_hosting_space_ids = [];
    public ?int $id_enterprise_dispatcher = null;

    /**
     * @param string $server_location_name
     * @param string $server_package_name
     * @param int $server_ip_amount
     * @param int[] $ignore_node_ids
     * @param int[] $ignore_hosting_space_ids
     * @param int|null $id_enterprise_dispatcher
     * @return static
     */
    #[Pure]
    public static function create(string $server_location_name, string $server_package_name, int $server_ip_amount, array $ignore_node_ids, array $ignore_hosting_space_ids, ?int $id_enterprise_dispatcher = null): self
    {
        $command = new self();
        $command->id_enterprise_dispatcher = $id_enterprise_dispatcher;
        $command->server_location_name = $server_location_name;
        $command->server_package_name = $server_package_name;
        $command->server_ip_amount = $server_ip_amount;
        $command->ignore_node_ids = $ignore_node_ids;
        $command->ignore_hosting_space_ids = $ignore_hosting_space_ids;
        return $command;
    }

    /**
     * @return int[]
     */
    public function getIgnoreNodeIds(): array
    {
        return $this->ignore_node_ids;
    }

    /** need for convert string array to int array after symfony denormalize
     * @param int[] $ignore_node_ids
     */
    public function setIgnoreNodeIds(array $ignore_node_ids): void
    {
        $this->ignore_node_ids = $this->stringArrayToInt($ignore_node_ids);
    }

    /**
     * @return int[]
     */
    public function getIgnoreHostingSpaceIds(): array
    {
        return $this->ignore_hosting_space_ids;
    }

    /** need for convert string array to int array after symfony denormalize
     * @param int[] $ignore_hosting_space_ids
     */
    public function setIgnoreHostingSpaceIds(array $ignore_hosting_space_ids): void
    {
        $this->ignore_hosting_space_ids = $this->stringArrayToInt($ignore_hosting_space_ids);
    }

    /**
     * @param array $arr
     * @return int[]
     */
    private function stringArrayToInt(array $arr): array
    {
        foreach ($arr as $one){
            if (!(is_int($one))) {
                //$this->ignore_hosting_space_ids = array_map('intval', array_values($arr));
                return array_map('intval', array_values($arr)); //convert array to int
            }
        }
        unset($one);
        return $arr; //all array were int
    }
}
