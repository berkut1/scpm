<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlan;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

final readonly class SolidcpHostingPlanFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(SolidcpHostingPlan::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
    }

    public function get(int $id): SolidcpHostingPlan
    {
        /** @var SolidcpHostingPlan $plan */
        if (!$plan = $this->repository->find($id)) {
            throw new NotFoundException('SolidcpHostingPlan is not found');
        }
        return $plan;
    }

    public function getDefault(): ?SolidcpHostingPlan
    {
        return $this->repository->findOneBy(['isDefault' => true]);
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'plans.id',
                'plans.name ||  \' (HostingSpace: \' || spaces.name || \')\' as name'
            )
            ->from('cp_solidcp_hosting_plans', 'plans')
            ->leftJoin('plans', 'cp_solidcp_hosting_spaces', 'spaces', 'spaces.id = plans.id_hosting_space')
            ->orderBy('name')
            ->executeQuery();

        return array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }

    public function allListWithSolidCpIdPlan(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'plans.solidcp_id_plan',
                'plans.name ||  \' (HostingSpace: \' || spaces.name || \')\' as name'
            )
            ->from('cp_solidcp_hosting_plans', 'plans')
            ->leftJoin('plans', 'cp_solidcp_hosting_spaces', 'spaces', 'spaces.id = plans.id_hosting_space')
            ->orderBy('name')
            ->executeQuery();

        return array_column($stmt->fetchAllAssociative(), 'name', 'solidcp_id_plan');
    }

    public function allPlansFromPackage(string $id_package, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
            )
            ->from('cp_solidcp_hosting_plans', 'plans')
            ->leftJoin('plans', 'cp_package_assigned_scp_hosting_plans', 'assignedPackages', 'assignedPackages.id_plan = plans.id')
            ->where('id_package = :id_package')
            ->setParameter('id_package', $id_package);

        if (!in_array($sort, ['name'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function allPlansFromSpace(int $id_hosting_space, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'solidcp_id_plan',
                'solidcp_id_server',
            )
            ->from('cp_solidcp_hosting_plans', 'plans')
            ->where('id_hosting_space = :id_hosting_space')
            ->setParameter('id_hosting_space', $id_hosting_space);

        if (!in_array($sort, ['name', 'solidcp_id_plan', 'solidcp_id_server'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'solidcp_id_plan',
                'solidcp_id_server',
            )
            ->from('cp_solidcp_hosting_plans', 'plans');

        if (!in_array($sort, ['name', 'name', 'solidcp_id_plan', 'solidcp_id_server'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}