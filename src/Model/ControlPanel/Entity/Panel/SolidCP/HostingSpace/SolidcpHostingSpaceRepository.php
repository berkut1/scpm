<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class SolidcpHostingSpaceRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(SolidcpHostingSpace::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(int $id): SolidcpHostingSpace
    {
        /** @var SolidcpHostingSpace $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('SolidCp Hosting space is not found.');
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

    public function add(SolidcpHostingSpace $object): void
    {
        $this->em->persist($object);
    }

    public function remove(SolidcpHostingSpace $object): void
    {
        $this->em->remove($object);
    }
}