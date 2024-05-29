<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\Node;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class SolidcpServerRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(SolidcpServer::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(int $id): SolidcpServer
    {
        /** @var SolidcpServer $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('SolidcpServer is not found.');
        }
        return $entity;
    }

    /**
     * @return SolidcpServer[]
     */
    public function getByLocation(int $id_location): array
    {
        $query = $this->repo->createQueryBuilder('t')
            ->where('t.location = :id_location')
            ->setParameter('id_location', $id_location)
            ->getQuery();
        return $query->execute();
    }

    public function hasByName(string $name): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.name = :name')
                ->setParameter('name', $name)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(SolidcpServer $object): void
    {
        $this->em->persist($object);
    }

    public function remove(SolidcpServer $object): void
    {
        $this->em->remove($object);
    }
}