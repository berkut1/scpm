<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class EnterpriseDispatcherFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(EnterpriseDispatcher::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
    }

    public function getDefault(): EnterpriseDispatcher
    {
        /** @var EnterpriseDispatcher $enterpriseDispatcher */
        if (!$enterpriseDispatcher = $this->repository->findOneBy(['isDefault' => true])) {
            throw new NotFoundException('EnterpriseDispatcher is not found');
        }
        return $enterpriseDispatcher;
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name ||  \' (Login: \' || login || \')\' as name'
            )
            ->from('cp_solidcp_enterprise_dispatchers')
            ->orderBy('name')
            ->executeQuery(); //execute() deprecated https://github.com/doctrine/dbal/pull/4578thub.com/doctrine/dbal/pull/4578;

        return array_column($stmt->fetchAllAssociative(), 'name','id');
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'url',
                'is_default',
                'enabled',
            )
            ->from('cp_solidcp_enterprise_dispatchers');

        if (!in_array($sort, ['name', 'url', 'is_default', 'enabled'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}