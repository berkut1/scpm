<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class AuditLogRepository
{
    protected EntityManagerInterface $em;
    protected EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(AuditLog::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(Id $id): AuditLog
    {
        /** @var AuditLog $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('AuditLog is not found.');
        }
        return $entity;
    }

    public function getBatch(int $batchSize, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): iterable
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')
            ->from(AuditLog::class, 'a')
            ->where($qb->expr()->between('a.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate);

        $query = $qb->getQuery();
        $query->setMaxResults($batchSize);
        return $query->toIterable();
    }

    public function add(AuditLog $object): void
    {
        $this->em->persist($object);
    }

    public function remove(AuditLog $item): void
    {
        $this->em->remove($item);
    }

    public function removeByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): int
    {
        //the best way? https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/batch-processing.html#dql-delete
        $query = $this->em->createQuery('DELETE FROM App\Model\AuditLog\Entity\AuditLog a WHERE a.date BETWEEN :start_date AND :end_date');
        $query->setParameter('start_date', $startDate);
        $query->setParameter('end_date', $endDate);
        return $query->execute();
    }

    public function removeBatch(int $batchSize, iterable $items): void
    {
        $i = 0;
        foreach ($items as $auditLog) {
            $this->remove($auditLog);
            ++$i;
            if (($i % $batchSize) === 0) {
                $this->em->flush(); // Executes all deletions.
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
        }
        //$this->em->flush(); call from handler?
    }
}