<?php
declare(strict_types=1);

namespace App\ReadModel\AuditLog;

use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

final readonly class AuditLogFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'logs.id',
                'logs.id_user',
                'users.login',
                'logs.date',
                'logs.ip_inet',
                'logs.entity_type',
                'logs.entity_id',
                'logs.task_name',
                'logs.records',
            )
            ->from('audit_logs', 'logs')
            ->leftJoin('logs', 'user_users', 'users', 'users.id = logs.id_user');

        if (!in_array($sort, ['login', 'date', 'ip_inet', 'entity_type', 'task_name'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}