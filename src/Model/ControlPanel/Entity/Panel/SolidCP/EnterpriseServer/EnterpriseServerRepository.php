<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class EnterpriseServerRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(EnterpriseServer::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(int $id): EnterpriseServer
    {
        /** @var EnterpriseServer $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('EnterpriseServer is not found.');
        }
        return $entity;
    }

    public function getDefaultOrById(?int $id = null): EnterpriseServer
    {
        if (empty($id) || $id < 1) { //if someone pass from API a default value
            $enterpriseServer = $this->getDefault();
        } else {
            $enterpriseServer = $this->get($id);
        }
        return $enterpriseServer;
    }

    public function getDefaultOrNull(): ?EnterpriseServer
    {
        return $this->repo->findOneBy(['isDefault' => true]);
    }

    public function getDefault(): EnterpriseServer
    {
        if (!$entity = $this->getDefaultOrNull()) {
            throw new EntityNotFoundException('EnterpriseServer is not found.');
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

    public function add(EnterpriseServer $object): void
    {
        $this->em->persist($object);
    }

    public function remove(EnterpriseServer $object): void
    {
        $this->em->remove($object);
    }
}