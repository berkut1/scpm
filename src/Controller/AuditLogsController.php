<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AuditLog\Entity\AuditLog;
use App\ReadModel\AuditLog\AuditLogFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/audit-logs', name: 'auditLogs')]
#[IsGranted('ROLE_ADMIN')]
class AuditLogsController extends AbstractController
{
    private const PER_PAGE = 25;
    private const MAIN_TITLE = 'Audit Logs';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('', name: '')]
    public function index(Request $request, AuditLogFetcher $fetcher): Response
    {
        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/audit_logs/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{id}', name: '.show')]
    public function show(AuditLog $auditLog): Response
    {
        return $this->render('app/audit_logs/show.html.twig', [
            'auditLog' => $auditLog,
        ]);
    }
}