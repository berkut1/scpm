<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package\VirtualMachine;

use App\Model\ControlPanel\Entity\Package\Id;
use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Package\PackageRepository;
use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class VirtualMachinePackageRepository extends PackageRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(VirtualMachinePackage::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(Id $id): VirtualMachinePackage
    {
        /** @var VirtualMachinePackage $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('VirtualMachinePackage is not found.');
        }
        return $entity;
    }

    public function getByName(string $name): VirtualMachinePackage
    {
        $virtualMachinePackage = $this->repo->createQueryBuilder('t')
            ->leftJoin(Package::class, 't2', Join::WITH,'t2.id = t.id')
            ->andWhere('t2.name = :name')
            ->setParameter('name', $name)
            ->getQuery()->getOneOrNullResult();

        if (!$virtualMachinePackage) {
            throw new EntityNotFoundException('VirtualMachinePackage is not found');
        }

        return $virtualMachinePackage;
    }

//    public function hasByName(string $name): bool
//    {
//        return $this->repo->createQueryBuilder('t')
//                ->select('COUNT(t2.id)')
//                ->leftJoin(Package::class, 't2', Join::WITH,'t2.id = t.id')
//                ->andWhere('t2.name = :name')
//                ->setParameter('name', $name)
//                ->getQuery()->getSingleScalarResult() > 0;
//    }

    public function add(VirtualMachinePackage $object): void
    {
        $this->em->persist($object);
    }
}