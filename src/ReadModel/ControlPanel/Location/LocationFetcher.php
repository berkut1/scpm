<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Location;

use App\Model\ControlPanel\Entity\Location\Location;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

final readonly class LocationFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(Location::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
    }

    public function getByName(string $name): Location
    {
        /** @var Location $location */
        if (!$location = $this->repository->findOneBy(['name' => $name])) {
            throw new NotFoundException('Location is not found');
        }
        return $location;
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('cp_locations')
            ->orderBy('name')
            ->executeQuery();

        return array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
            )
            ->from('cp_locations');

        if (!in_array($sort, ['name'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}