<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Panel\SolidCP\Node\HostingSpace;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

final readonly class SolidcpHostingSpaceFetcher
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

    public function get(int $id): SolidcpHostingSpace
    {
        /** @var SolidcpHostingSpace $hostingSpace */
        if (!$hostingSpace = $this->repository->find($id)) {
            throw new NotFoundException('SolidcpHostingSpace is not found');
        }
        return $hostingSpace;
    }

    public function allHostingSpaceFromNode(int $id_server, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'solidcp_id_hosting_space',
                'name',
                'max_active_number',
                'max_reserved_memory_kb',
                'space_quota_gb',
                'enabled',
            )
            ->from('cp_solidcp_hosting_spaces', 'hosting_space')
            ->where('id_server = :id_server')
            ->setParameter('id_server', $id_server);

        if (!in_array($sort, ['solidcp_id_hosting_space', 'name', 'max_active_number', 'max_reserved_memory_kb', 'space_quota_gb', 'enabled'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}