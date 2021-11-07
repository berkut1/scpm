<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class SolidcpHostingSpaceFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(SolidcpHostingSpace::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('cp_solidcp_hosting_spaces')
            ->orderBy('name')
            ->executeQuery(); //execute() deprecated https://github.com/doctrine/dbal/pull/4578thub.com/doctrine/dbal/pull/4578;

        return array_column($stmt->fetchAllAssociative(), 'name','id');
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'spaces.id',
                'spaces.name',
                'servers.name as server_name',
                'locations.name as location_name',
                'spaces.solidcp_id_hosting_space',
                'spaces.max_active_number',
                'spaces.max_reserved_memory_kb',
                'spaces.space_quota_gb',
                'spaces.enabled',
                '(SELECT COUNT(plans.id) FROM cp_solidcp_hosting_plans AS plans WHERE plans.id_hosting_space = spaces.id) as num_of_plans',
                '(SELECT COUNT(templates.id) FROM cp_solidcp_hosting_space_os_templates AS templates WHERE templates.id_hosting_space = spaces.id) as num_of_templates',
            )
            ->from('cp_solidcp_hosting_spaces', 'spaces')
            ->leftJoin('spaces', 'cp_solidcp_servers', 'servers', 'servers.id = spaces.id_server')
            ->leftJoin('servers', 'cp_locations', 'locations', 'locations.id = servers.id_location');

        if (!in_array($sort, ['name', 'server_name', 'location_name', 'solidcp_id_hosting_space', 'max_active_number', 'max_reserved_memory_kb', 'space_quota_gb', 'enabled', 'num_of_plans', 'num_of_templates'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}