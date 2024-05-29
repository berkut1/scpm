<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcher;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\{Create, Disable, Edit, Enable, Remove, SetDefault};
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panel/solidcp/enterprise-dispatchers', name: 'enterpriseDispatchers')]
#[IsGranted('ROLE_MODERATOR')]
final class EnterpriseDispatchersController extends AbstractController
{
    private const int PER_PAGE = 25;
    private const string MAIN_TITLE = 'Enterprise Dispatchers';

    public function __construct(private readonly LoggerInterface $logger) {}

    #[Route('', name: '')]
    public function index(Request $request, EnterpriseDispatcherFetcher $fetcher): Response
    {

        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/control_panel/solidcp/enterprise_dispatchers/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/create', name: '.create')]
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('enterpriseDispatchers');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/enterprise_dispatchers/create.html.twig', [
            'page_title' => 'Add Enterprise Dispatcher',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', requirements: ['id' => Requirement::DIGITS])]
    public function edit(EnterpriseDispatcher $enterpriseDispatcher, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromEnterpriseDispatcher($enterpriseDispatcher);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('enterpriseDispatchers');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/enterprise_dispatchers/edit.html.twig', [
            'page_title' => 'Edit Enterprise Dispatcher',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/enable', name: '.enable', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function enable(int $id, Request $request, Enable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('enable', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseDispatchers');
        }

        $command = new Enable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseDispatchers');
    }

    #[Route('/{id}/disable', name: '.disable', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function disable(int $id, Request $request, Disable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('disable', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseDispatchers');
        }

        $command = new Disable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseDispatchers');
    }

    #[Route('/{id}/set-default', name: '.setDefault', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function setDefault(EnterpriseDispatcher $enterpriseDispatcher, Request $request, SetDefault\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('setDefault', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseDispatchers');
        }

        $command = new SetDefault\Command($enterpriseDispatcher->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseDispatchers');
    }

    #[Route('/{id}/remove', name: '.remove', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function remove(int $id, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseDispatchers');
        }

        $command = new Remove\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseDispatchers');
    }
}