<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Panel\SolidCP\Node;

use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

final readonly class SolidcpServerFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(SolidcpServer::class);
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
            ->from('cp_solidcp_servers')
            ->orderBy('name')
            ->executeQuery();

        return array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }

    public function allListFrom(int $id_enterprise_dispatcher): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('cp_solidcp_servers')
            ->where('id_enterprise_dispatcher = :id_enterprise_dispatcher')
            ->setParameter('id_enterprise_dispatcher', $id_enterprise_dispatcher)
            ->orderBy('name')
            ->executeQuery();

        return array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'servers.id',
                'enterprises.name as enterprise_name',
                'locations.name as location_name',
                'servers.name',
                'servers.cores',
                'servers.threads',
                'servers.memory_mb',
                'servers.enabled',
            )
            ->from('cp_solidcp_servers', 'servers')
            ->leftJoin('servers', 'cp_solidcp_enterprise_dispatchers', 'enterprises', 'enterprises.id = servers.id_enterprise_dispatcher')
            ->leftJoin('servers', 'cp_locations', 'locations', 'locations.id = servers.id_location');

        if (!in_array($sort, ['enterprise_name', 'location_name', 'name', 'memory_mb', 'enabled'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}