<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AuditLogRepository
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

    public function getLog(Id $id): AuditLog
    {
        /** @var AuditLog $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('AuditLog is not found.');
        }
        return $entity;
    }

    public function add(AuditLog $object): void
    {
        $this->em->persist($object);
    }

    public function remove(AuditLog $item): void
    {
        $this->em->remove($item);
    }
}