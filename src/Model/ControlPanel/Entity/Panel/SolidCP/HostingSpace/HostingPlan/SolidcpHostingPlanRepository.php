<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\HostingPlan;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class SolidcpHostingPlanRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(SolidcpHostingPlan::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(int $id): SolidcpHostingPlan
    {
        /** @var SolidcpHostingPlan $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('SolidCp Hosting plan is not found.');
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
}