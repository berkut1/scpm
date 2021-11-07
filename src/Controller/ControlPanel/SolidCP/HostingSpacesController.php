<?php 
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\OsTemplate\OsTemplateFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\SolidcpHostingSpaceFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/panel/solidcp/hosting-spaces", name="solidCpHostingSpaces")
 * @IsGranted("ROLE_MODERATOR")
 */
class HostingSpacesController extends AbstractController
{
    private const PER_PAGE = 25;
    private const MAIN_TITLE = 'Hosting Spaces';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @param SolidcpHostingSpaceFetcher $fetcher
     * @return Response
     */
    public function index(Request $request, SolidcpHostingSpaceFetcher $fetcher): Response
    {

        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/control_panel/solidcp/hosting_spaces/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/create", name=".create")
     * @param Request $request
     * @param HostingSpace\Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, HostingSpace\Create\Handler $handler): Response
    {
        $command = new HostingSpace\Create\Command();

        $form = $this->createForm(HostingSpace\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpHostingSpaces');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/hosting_spaces/create.html.twig', [
            'page_title' => 'Add Hosting Space',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param Request $request
     * @param HostingSpace\Edit\Handler $handler
     * @return Response
     */
    public function edit(SolidcpHostingSpace $solidcpHostingSpace, Request $request, HostingSpace\Edit\Handler $handler): Response
    {
        $command = HostingSpace\Edit\Command::fromHostingSpace($solidcpHostingSpace);

        $form = $this->createForm(HostingSpace\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/hosting_spaces/edit.html.twig', [
            'page_title' => 'Edit Hosting Space',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'solidcpHostingSpace' => $solidcpHostingSpace,
        ]);
    }

    /**
     * @Route("/{id}/change-node", name=".changeNode")
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param Request $request
     * @param HostingSpace\ChangeNode\Handler $handler
     * @return Response
     */
    public function changeNode(SolidcpHostingSpace $solidcpHostingSpace, Request $request, HostingSpace\ChangeNode\Handler $handler): Response
    {
        $command = HostingSpace\ChangeNode\Command::fromHostingSpace($solidcpHostingSpace);

        $form = $this->createForm(HostingSpace\ChangeNode\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/hosting_spaces/change_node.html.twig', [
            'page_title' => 'Change Hosting Space Node',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'solidcpHostingSpace' => $solidcpHostingSpace,
        ]);
    }

    /**
     * @Route("/{id}/change-solidcp-hosting-space-id", name=".changeSolidCpHostingSpaceId")
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param Request $request
     * @param HostingSpace\ChangeSolidCpHostingSpace\Handler $handler
     * @return Response
     */
    public function changeSolidCpHostingSpaceId(SolidcpHostingSpace $solidcpHostingSpace, Request $request, HostingSpace\ChangeSolidCpHostingSpace\Handler $handler): Response
    {
        $command = HostingSpace\ChangeSolidCpHostingSpace\Command::fromHostingSpace($solidcpHostingSpace);

        $form = $this->createForm(HostingSpace\ChangeSolidCpHostingSpace\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/solidcp/hosting_spaces/change_soldicp_hosting_space_id.html.twig', [
            'page_title' => 'Change SolidCP Hosting Space ID',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'solidcpHostingSpace' => $solidcpHostingSpace,
        ]);
    }

    /**
     * @Route("/{id}/enable", name=".enable", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param HostingSpace\Enable\Handler $handler
     * @return Response
     */
    public function enable(int $id, Request $request, HostingSpace\Enable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('enable', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
        }

        $command = new HostingSpace\Enable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
    }

    /**
     * @Route("/{id}/disable", name=".disable", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param HostingSpace\Disable\Handler $handler
     * @return Response
     */
    public function disable(int $id, Request $request, HostingSpace\Disable\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('disable', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
        }

        $command = new HostingSpace\Disable\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
    }

    /**
     * @Route("/{id}/add-plan", name=".addPlan")
     * @param Request $request
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param HostingSpace\HostingPlan\Add\Handler $handler
     * @return Response
     */
    public function addPlan(Request $request, SolidcpHostingSpace $solidcpHostingSpace, HostingSpace\HostingPlan\Add\Handler $handler): Response
    {
        $command = new HostingSpace\HostingPlan\Add\Command($solidcpHostingSpace->getId());

        $form = $this->createForm(HostingSpace\HostingPlan\Add\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/hosting_spaces/add_plan.html.twig', [
            'page_title' => 'Add Plan',
            'main_title' => self::MAIN_TITLE,
            'hostingSpace' => $solidcpHostingSpace,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/plan/{id_plan}/remove", name=".removePlan", methods={"POST"})
     * @param int $id
     * @param int $id_plan
     * @param Request $request
     * @param HostingSpace\HostingPlan\Remove\Handler $handler
     * @return Response
     */
    public function removePlan(int $id, int $id_plan, Request $request, HostingSpace\HostingPlan\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('removePlan', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
        }

        $command = new HostingSpace\HostingPlan\Remove\Command($id, $id_plan);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
    }

    /**
     * @Route("/{id}/plan/{id_plan}/set-default", name=".setDefaultPlan", methods={"POST"})
     * @ParamConverter("solidcpHostingPlan", options={"mapping": {"id_plan": "id"}})
     * @param int $id
     * @param int $id_plan
     * @param SolidcpHostingPlanFetcher $solidcpHostingPlanFetcher
     * @param Request $request
     * @param HostingSpace\HostingPlan\SetDefault\Handler $handler
     * @return Response
     */
    public function setDefaultPlan(int $id, int $id_plan, SolidcpHostingPlanFetcher $solidcpHostingPlanFetcher, Request $request, HostingSpace\HostingPlan\SetDefault\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('setDefault', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
        }

        $plan = $solidcpHostingPlanFetcher->get($id_plan);

        $command = new HostingSpace\HostingPlan\SetDefault\Command($plan->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $id]);
    }

    /**
     * @Route("/{id}/add-os-template", name=".addOsTemplate")
     * @param Request $request
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param HostingSpace\OsTemplate\Add\Handler $handler
     * @return Response
     */
    public function addOsTemplate(Request $request, SolidcpHostingSpace $solidcpHostingSpace, HostingSpace\OsTemplate\Add\Handler $handler): Response
    {
        $command = HostingSpace\OsTemplate\Add\Command::fromHostingSpace($solidcpHostingSpace);

        $form = $this->createForm(HostingSpace\OsTemplate\Add\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/hosting_spaces/os_templates/edit.html.twig', [
            'page_title' => 'Add Os Template',
            'main_title' => self::MAIN_TITLE,
            'hostingSpace' => $solidcpHostingSpace,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/os-template/{id_os_template}/remove", name=".removeOsTemplate", methods={"POST"})
     * @param Request $request
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param HostingSpace\OsTemplate\Remove\Handler $handler
     * @param int $id_os_template
     * @return Response
     */
    public function removeOsTemplate(Request $request, SolidcpHostingSpace $solidcpHostingSpace, HostingSpace\OsTemplate\Remove\Handler $handler, int $id_os_template): Response
    {
        if (!$this->isCsrfTokenValid('removeOsTemplate', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
        }

        $command = new HostingSpace\OsTemplate\Remove\Command($solidcpHostingSpace->getId(), $id_os_template);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpHostingSpaces.show', ['id' => $solidcpHostingSpace->getId()]);
    }

    /**
     * @Route("/{id}/show", name=".show")
     * @param Request $request
     * @param SolidcpHostingSpace $solidcpHostingSpace
     * @param SolidcpHostingPlanFetcher $planFetcher
     * @param OsTemplateFetcher $osTemplateFetcher
     * @return Response
     */
    public function show(Request $request, SolidcpHostingSpace $solidcpHostingSpace, SolidcpHostingPlanFetcher $planFetcher, OsTemplateFetcher $osTemplateFetcher): Response
    {
        $hostingPlans = $planFetcher->allPlansFromSpace(
            $solidcpHostingSpace->getId(),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'asc'));

        $hostingTemplates = $osTemplateFetcher->allOsTemplatesFromSpace(
            $solidcpHostingSpace->getId(),
            $request->query->getInt('TemplatePage', 1),
            self::PER_PAGE,
            $request->query->get('TemplateSort', 'name'),
            $request->query->get('TemplateDirection', 'asc'));
        return $this->render('app/control_panel/solidcp/hosting_spaces/show.html.twig',
            [
                'main_title' => self::MAIN_TITLE,
                'hostingSpace' => $solidcpHostingSpace,
                'hostingPlans' => $hostingPlans,
                'hostingTemplates' => $hostingTemplates,
            ]
        );
    }

    /**
     * @Route("/{id}/remove", name=".remove", methods={"POST"})
     * @param int $id,
     * @param Request $request
     * @param HostingSpace\Remove\Handler $handler
     * @return Response
     */
    public function remove(int $id, Request $request, HostingSpace\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('solidCpHostingSpaces');
        }

        $command = new HostingSpace\Remove\Command($id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('solidCpHostingSpaces');
    }
}