<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\Package\VirtualMachine;

use App\Model\ControlPanel\Entity\Package\Package;
use App\Model\ControlPanel\Entity\Package\VirtualMachine\VirtualMachinePackage;
use App\Model\ControlPanel\UseCase\Package\{ChangePlans, Remove, Rename};
use App\Model\ControlPanel\UseCase\Package\VirtualMachine\{Create, Edit};
use App\ReadModel\ControlPanel\Package\VirtualMachine\VirtualMachinePackageFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/packages/virtual-machines', name: 'virtualMachinePackages')]
#[IsGranted('ROLE_MODERATOR')]
final class VirtualMachinePackagesController extends AbstractController
{
    private const int PER_PAGE = 25;
    private const string MAIN_TITLE = 'Virtual Machine Packages';

    public function __construct(private readonly LoggerInterface $logger) {}

    #[Route('', name: '')]
    public function index(Request $request, VirtualMachinePackageFetcher $fetcher): Response
    {

        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/control_panel/packages/virtual_machines/index.html.twig', [
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
                return $this->redirectToRoute('virtualMachinePackages');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/packages/virtual_machines/create.html.twig', [
            'page_title' => 'Add Virtual Machine Package',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/rename', name: '.rename', requirements: ['id' => Requirement::UID_RFC4122])]
    public function rename(VirtualMachinePackage $virtualMachinePackage, Request $request, Rename\Handler $handler): Response
    {
        $command = Rename\Command::fromPackage($virtualMachinePackage->getPackage());

        $form = $this->createForm(Rename\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('virtualMachinePackages.show', ['id' => $virtualMachinePackage->getId()->getValue()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/packages/virtual_machines/edit_rename.html.twig', [
            'page_title' => 'Rename Virtual Machine Package',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'virtualMachinePackage' => $virtualMachinePackage,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', requirements: ['id' => Requirement::UID_RFC4122])]
    public function edit(VirtualMachinePackage $virtualMachinePackage, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromVirtualMachine($virtualMachinePackage);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('virtualMachinePackages.show', ['id' => $virtualMachinePackage->getId()->getValue()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/packages/virtual_machines/edit.html.twig', [
            'page_title' => 'Edit Virtual Machine Package',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
            'virtualMachinePackage' => $virtualMachinePackage,
        ]);
    }

    #[Route('/{id}', name: '.show', requirements: ['id' => Requirement::UID_RFC4122])]
    public function show(Request $request, VirtualMachinePackage $virtualMachinePackage, SolidcpHostingPlanFetcher $planFetcher): Response
    {
        $plansFromPackage = $planFetcher->allPlansFromPackage(
            $virtualMachinePackage->getId()->getValue(),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'asc'));
        return $this->render('app/control_panel/packages/virtual_machines/show.html.twig',
            [
                'main_title' => self::MAIN_TITLE,
                'virtualMachinePackage' => $virtualMachinePackage,
                'plansFromPackage' => $plansFromPackage,
            ]
        );
    }

    #[Route('/{id}/change-solidcp-plans', name: '.changeSolidCpPlans', requirements: ['id' => Requirement::UID_RFC4122])]
    public function changeSolidCpPlans(
        Request $request, VirtualMachinePackage $virtualMachinePackage, ChangePlans\SolidCP\Handler $handler
    ): Response
    {
        $command = ChangePlans\SolidCP\Command::fromPackage($virtualMachinePackage->getPackage());

        $form = $this->createForm(ChangePlans\SolidCP\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('virtualMachinePackages.show', ['id' => $virtualMachinePackage->getId()->getValue()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/packages/virtual_machines/change_solidcp_plans.html.twig', [
            'page_title' => 'Assign Plan',
            'main_title' => self::MAIN_TITLE,
            'virtualMachinePackage' => $virtualMachinePackage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/remove', name: '.remove', requirements: ['id' => Requirement::UID_RFC4122], methods: ['POST'])]
    public function remove(Package $location, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('virtualMachinePackages');
        }

        $command = new Remove\Command($location->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('virtualMachinePackages');
    }
}