<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Package;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class PackageRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(Package::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function getPackage(Id $id): Package
    {
        /** @var Package $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('Package is not found.');
        }
        return $entity;
    }

    public function hasByName(string $name): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.name = :name')
                ->setParameter('name', $name)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function remove(Package $object): void
    {
        $this->em->remove($object);
    }
}