<?php
declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use App\ReadModel\CustomFetcher;
use App\ReadModel\NotFoundException;
use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;
    private CustomFetcher $customFetcher;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator, CustomFetcher $customFetcher)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(User::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
        $this->customFetcher = $customFetcher;
    }

    public function findForAuthByLogin(string $login): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'login',
                'password',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('LOWER(login) = :login')
            ->setParameter('login', mb_strtolower($login)) //deprecated colon prefix for parameters -  :logon -> logon
            //->execute(); //deprecated https://github.com/doctrine/dbal/pull/4578'
            ->executeQuery();

//        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class); //deprecated
//        $result = $stmt->fetch();
        $result = $this->customFetcher->fetchCustomObject($stmt, new AuthView);

        return $result ?: null;
    }

    public function get(string $id): User
    {
        /** @var User $user */
        if (!$user = $this->repository->find($id)) {
            throw new NotFoundException('User is not found');
        }
        return $user;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'date',
                'login',
                'role',
                'status'
            )
            ->from('user_users');

        if ($filter->login) {
            $qb->andWhere($qb->expr()->like('LOWER(login)', ':login'));
            $qb->setParameter('login', '%' . mb_strtolower($filter->login) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter('status', mb_strtolower($filter->status));
        }

        if ($filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter('role', $filter->role);
        }

        if (!in_array($sort, ['date', 'login', 'role', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
