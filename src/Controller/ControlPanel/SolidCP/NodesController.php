<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\Entity\Panel\SolidCP\Node\SolidcpServer;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\Create as CreateHostingSpace;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\ChangeNode;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\{Create, Edit, Enable, Disable, Remove};
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\HostingSpace\SolidcpHostingSpaceFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/panel/solidcp/node-servers', name: 'solidCpServers')]
#[IsGranted('ROLE_MODERATOR')]
class NodesController extends AbstractController
{
    private const PER_PAGE = 25;
    private const MAIN_TITLE = 'Node Servers';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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

    #[Route('/create-hosting-space', name: '.createHostingSpace')]
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

    #[Route('/{id}/edit', name: '.edit')]
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

    #[Route('/{id}/enable', name: '.enable', methods: ['POST'])]
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

    #[Route('/{id}/disable', name: '.disable', methods: ['POST'])]
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

    #[Route('/{id}', name: '.show')]
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

    #[Route('/{id}/hosting-spaces/{id_hosting_space}/change-node', name: '.changeNode')]
    #[ParamConverter('solidcpHostingSpace', options: ['mapping' => ['id_hosting_space' => 'id']])]
    public function changeNode(int $id, SolidcpHostingSpace $solidcpHostingSpace, Request $request, ChangeNode\Handler $handler): Response
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

    #[Route('/{id}/remove', name: '.remove', methods: ['POST'])]
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