<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class EnterpriseDispatcherRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(EnterpriseDispatcher::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(int $id): EnterpriseDispatcher
    {
        /** @var EnterpriseDispatcher $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('EnterpriseDispatcher is not found.');
        }
        return $entity;
    }

    public function getDefaultOrById(?int $id = null): EnterpriseDispatcher
    {
        if (empty($id) || $id < 1) { //if someone pass from API a default value
            $enterpriseDispatcher = $this->getDefault();
        } else {
            $enterpriseDispatcher = $this->get($id);
        }
        return $enterpriseDispatcher;
    }

    public function getDefaultOrNull(): ?EnterpriseDispatcher
    {
        return $this->repo->findOneBy(['isDefault' => true]);
    }

    public function getDefault(): EnterpriseDispatcher
    {
        if (!$entity = $this->getDefaultOrNull()) {
            throw new EntityNotFoundException('EnterpriseDispatcher is not found.');
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

    public function add(EnterpriseDispatcher $object): void
    {
        $this->em->persist($object);
    }

    public function remove(EnterpriseDispatcher $object): void
    {
        $this->em->remove($object);
    }
}