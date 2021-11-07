<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\SetDefault;
use App\Model\ControlPanel\Entity\Panel\SolidCP\EnterpriseServer\EnterpriseServer;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Create;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Edit;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Enable;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Disable;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseServer\Remove;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/panel/solidcp/enterprise-servers", name="enterpriseServers")
 * @IsGranted("ROLE_MODERATOR")
 */
class EnterpriseServersController extends AbstractController
{
    private const PER_PAGE = 25;
    private const MAIN_TITLE = 'Enterprise Servers';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @param EnterpriseServerFetcher $fetcher
     * @return Response
     */
    public function index(Request $request, EnterpriseServerFetcher $fetcher): Response
    {

        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/control_panel/solidcp/enterprise_servers/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/create", name=".create")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('enterpriseServers');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/enterprise_servers/create.html.twig', [
            'page_title' => 'Add Enterprise Server',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     * @param EnterpriseServer $enterpriseServer
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    public function edit(EnterpriseServer $enterpriseServer, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromEnterpriseServer($enterpriseServer);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('enterpriseServers');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/enterprise_servers/edit.html.twig', [
            'page_title' => 'Edit Enterprise Server',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/enable", name=".enable", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param Enable\Handler $handler
     * @return Response
     */
    public function enable(int $id, Request $request, Enable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('enable', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseServers');
        }

        $command = new Enable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseServers');
    }

    /**
     * @Route("/{id}/disable", name=".disable", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param Disable\Handler $handler
     * @return Response
     */
    public function disable(int $id, Request $request, Disable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('disable', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseServers');
        }

        $command = new Disable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseServers');
    }

    /**
     * @Route("/{id}/set-default", name=".setDefault", methods={"POST"})
     * @param EnterpriseServer $enterpriseServer
     * @param Request $request
     * @param SetDefault\Handler $handler
     * @return Response
     */
    public function setDefault(EnterpriseServer $enterpriseServer, Request $request, SetDefault\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('setDefault', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseServers');
        }

        $command = new SetDefault\Command($enterpriseServer->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseServers');
    }

    /**
     * @Route("/{id}/remove", name=".remove", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    public function remove(int $id, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('enterpriseServers');
        }

        $command = new Remove\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('enterpriseServers');
    }
}