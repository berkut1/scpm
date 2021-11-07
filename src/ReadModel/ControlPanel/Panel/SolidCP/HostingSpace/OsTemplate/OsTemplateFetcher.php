<?php
declare(strict_types=1);

namespace App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\OsTemplate;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplate;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class OsTemplateFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(OsTemplate::class);
        $this->repository = $repo;
        $this->paginator = $paginator;
    }

    public function get(int $id): OsTemplate
    {
        /** @var OsTemplate $template */
        if (!$template = $this->repository->find($id)) {
            throw new NotFoundException('OsTemplate is not found');
        }
        return $template;
    }

    public function allOsTemplatesFromSpace(int $id_hosting_space, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'path',
                'name',
            )
            ->from('cp_solidcp_hosting_space_os_templates', 'plans')
            ->where('id_hosting_space = :id_hosting_space')
            ->setParameter('id_hosting_space', $id_hosting_space);

        if (!in_array($sort, ['path', 'name'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size,
            [   //Change default parameters to prevent conflict with two paginators on one page
                'pageParameterName' => 'TemplatePage',
                'sortDirectionParameterName' => 'TemplateDirection',
                'sortFieldParameterName' => 'TemplateSort',
            ]);
    }
}