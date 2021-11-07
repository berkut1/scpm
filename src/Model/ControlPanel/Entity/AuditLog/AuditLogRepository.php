<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\AuditLog;

use App\Model\AuditLog\Entity\AuditLog;
use App\Model\AuditLog\Entity\Id;
use App\Model\EntityNotFoundException;

class AuditLogRepository extends \App\Model\AuditLog\Entity\AuditLogRepository
{
    public function get(Id $id): AuditLog
    {
        /** @var AuditLog $entity */
        if (!$entity = $this->repo->find($id)) {
            throw new EntityNotFoundException('AuditLog is not found.');
        }
        return $entity;
    }


}