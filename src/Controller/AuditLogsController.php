<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AuditLog\Entity\AuditLog;
use App\Model\AuditLog\UseCase\AuditLog\{Remove};
use App\ReadModel\AuditLog\AuditLogFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/audit-logs', name: 'auditLogs')]
#[IsGranted('ROLE_ADMIN')]
final class AuditLogsController extends AbstractController
{
    private const int PER_PAGE = 25;
    private const string MAIN_TITLE = 'Audit Logs';

    public function __construct(private readonly LoggerInterface $logger) {}

    #[Route('', name: '')]
    public function index(Request $request, AuditLogFetcher $fetcher, Remove\Batch\Handler $handler): Response
    {
        $command = new Remove\Batch\Command();
        $form = $this->createForm(Remove\Batch\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/audit_logs/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
            'pagination' => $pagination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '.show', requirements: ['solidcp_item_id' => Requirement::UID_RFC4122])]
    public function show(AuditLog $auditLog): Response
    {
        return $this->render('app/audit_logs/show.html.twig', [
            'auditLog' => $auditLog,
        ]);
    }
}