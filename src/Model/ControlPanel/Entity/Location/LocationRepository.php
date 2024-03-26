<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Location;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class LocationRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(Location::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(int $id): Location
    {
        /** @var Location $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('Location is not found.');
        }
        return $entity;
    }

    public function getByName(string $name): Location
    {
        /** @var Location $entity */
        if (!$entity = $this->repo->findOneBy(['name' => $name])) {
            throw new EntityNotFoundException('Location is not found.');
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

    public function add(Location $object): void
    {
        $this->em->persist($object);
    }

    public function remove(Location $object): void
    {
        $this->em->remove($object);
    }
}