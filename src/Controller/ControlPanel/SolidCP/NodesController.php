<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\{Create, Disable, Edit, Enable, Remove};
use App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\ChangeNode;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\Create as CreateHostingSpace;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\HostingSpace\SolidcpHostingSpaceFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panel/solidcp/node-servers', name: 'solidCpServers')]
#[IsGranted('ROLE_MODERATOR')]
final class NodesController extends AbstractController
{
    private const int PER_PAGE = 25;
    private const string MAIN_TITLE = 'Node Servers';

    public function __construct(private readonly LoggerInterface $logger) {}

    #[Route('', name: '')]
    public function index(Request $request, SolidcpServerFetcher $fetcher): Response
    {
        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/control_panel/solidcp/nodes/index.html.twig', [
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
                return $this->redirectToRoute('solidCpServers');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/nodes/create.html.twig', [
            'page_title' => 'Add SolidCP Server',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/create-hosting-space', name: '.createHostingSpace', requirements: ['id' => Requirement::DIGITS])]
    public function createHostingSpace(SolidcpServer $solidcpServer, Request $request, CreateHostingSpace\Handler $handler): Response
    {
        $command = CreateHostingSpace\Command::fromServer($solidcpServer);

        $form = $this->createForm(CreateHostingSpace\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpServers.show', ['id' => $solidcpServer->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/nodes/hosting_spaces/create.html.twig', [
            'page_title' => 'Add hosting Spaces',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'solidcpServer' => $solidcpServer,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', requirements: ['id' => Requirement::DIGITS])]
    public function edit(SolidcpServer $solidcpServer, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromSolidcpServer($solidcpServer);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpServers.show', ['id' => $solidcpServer->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/nodes/edit.html.twig', [
            'page_title' => 'Edit SolidCP Server',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'solidcpServer' => $solidcpServer,
        ]);
    }

    #[Route('/{id}/enable', name: '.enable', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function enable(int $id, Request $request, Enable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('enable', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpServers');
        }

        $command = new Enable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpServers');
    }

    #[Route('/{id}/disable', name: '.disable', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function disable(int $id, Request $request, Disable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('disable', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpServers');
        }

        $command = new Disable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpServers');
    }

    #[Route('/{id}', name: '.show', requirements: ['id' => Requirement::DIGITS])]
    public function show(Request $request, SolidcpServer $solidcpServer, SolidcpHostingSpaceFetcher $hostingSpaceFetcher): Response
    {
        $spaceFromNode = $hostingSpaceFetcher->allHostingSpaceFromNode(
            $solidcpServer->getId(),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'asc'));

        return $this->render('app/control_panel/solidcp/nodes/show.html.twig',
            [
                'main_title' => self::MAIN_TITLE,
                'solidcpServer' => $solidcpServer,
                'spaceFromNode' => $spaceFromNode,
            ]
        );
    }

    #[Route('/{id}/hosting-spaces/{id_hosting_space}/change-node', name: '.changeNode', requirements: ['id' => Requirement::DIGITS, 'id_hosting_space' => Requirement::DIGITS])]
    public function changeNode(
        int                                                                     $id,
        #[MapEntity(mapping: ['id_hosting_space' => 'id'])] SolidcpHostingSpace $solidcpHostingSpace,
        Request                                                                 $request,
        ChangeNode\Handler                                                      $handler
    ): Response
    {
        $command = ChangeNode\Command::fromHostingSpace($solidcpHostingSpace);

        $form = $this->createForm(ChangeNode\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpServers.show', ['id' => $id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/nodes/hosting_spaces/change_node.html.twig', [
            'page_title' => 'Change Hosting Space Node',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'solidcpHostingSpace' => $solidcpHostingSpace,
        ]);
    }

    #[Route('/{id}/remove', name: '.remove', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function remove(int $id, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpServers');
        }

        $command = new Remove\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpServers');
    }
}