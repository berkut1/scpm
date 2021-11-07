<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Package\VirtualMachine;

use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class VirtualMachinePackageFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(VirtualMachinePackage::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'vms.id_package as id',
                'packages.name'
            )
            ->from('cp_package_virtual_machines', 'vms')
            ->leftJoin('vms', 'cp_packages', 'packages', 'packages.id_package = vms.id_package')
            ->orderBy('name')
            ->executeQuery(); //execute() deprecated https://github.com/doctrine/dbal/pull/4578thub.com/doctrine/dbal/pull/4578;

        return array_column($stmt->fetchAllAssociative(), 'name','id');
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'vms.id_package as id',
                'packages.name',
                'packages.package_type',
                'vms.cores',
                'vms.threads',
                'vms.ram_mb',
                'vms.space_gb',
            )
            ->from('cp_package_virtual_machines', 'vms')
            ->leftJoin('vms', 'cp_packages', 'packages', 'packages.id_package = vms.id_package');

        if (!in_array($sort, ['name', 'package_type', 'cores', 'ram_mb', 'space_gb'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}