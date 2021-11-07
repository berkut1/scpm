<?php
declare(strict_types=1);

namespace App\Controller\ControlPanel\SolidCP;

use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package as SOAPPackage;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012 as SOAPVirtualizationServer2012;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\AllinOne as SOAPAllinOne;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create as SOAPUserCreate;
use App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Check as SOAPUserCheck;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/panel/solidcp/debug", name="solidcpDebug")
 * @IsGranted("ROLE_USER")
 */
class DebugController extends AbstractController
{
    private const MAIN_TITLE = 'Debug Menu';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('app/control_panel/solidcp/debug/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
        ]);
    }

    /**
     * @Route("/create-user", name=".createUser")
     * @param Request $request
     * @param SOAPUserCreate\Handler $handler
     * @return Response
     */
    public function createUser(Request $request, SOAPUserCreate\Handler $handler): Response
    {
        $command = new SOAPUserCreate\Command();

        $form = $this->createForm(SOAPUserCreate\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidcpDebug');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/debug/user/create.html.twig', [
            'page_title' => 'Add User',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user-exists", name=".userExists")
     * @param Request $request
     * @param SOAPUserCheck\Handler $handler
     * @return Response
     */
    public function userExists(Request $request, SOAPUserCheck\Handler $handler): Response
    {
        $command = new SOAPUserCheck\Command();

        $form = $this->createForm(SOAPUserCheck\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidcpDebug');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/debug/user/check.html.twig', [
            'page_title' => 'Check User',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create-package", name=".createPackage")
     * @param Request $request
     * @param SOAPPackage\Create\Handler $handler
     * @return Response
     */
    public function createPackage(Request $request, SOAPPackage\Create\Handler $handler): Response
    {
        $command = new SOAPPackage\Create\Command();

        $form = $this->createForm(SOAPPackage\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidcpDebug');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/debug/package/create.html.twig', [
            'page_title' => 'Create package',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create-vm", name=".createVM")
     * @param Request $request
     * @param SOAPVirtualizationServer2012\CreateVM\Handler $handler
     * @return Response
     */
    public function createVM(Request $request, SOAPVirtualizationServer2012\CreateVM\Handler $handler): Response
    {
        $command = new SOAPVirtualizationServer2012\CreateVM\Command();

        $form = $this->createForm(SOAPVirtualizationServer2012\CreateVM\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidcpDebug');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/debug/vms/create.html.twig', [
            'page_title' => 'Create package',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check-vm-provisioning-status", name=".checkVMProvisioningStatus")
     * @param Request $request
     * @param SOAPVirtualizationServer2012\Check\VpsProvisioningStatus\Handler $handler
     * @return Response
     */
    public function checkVMProvisioningStatus(Request $request, SOAPVirtualizationServer2012\Check\VpsProvisioningStatus\Handler $handler): Response
    {
        $command = new SOAPVirtualizationServer2012\Check\VpsProvisioningStatus\Command();

        $form = $this->createForm(SOAPVirtualizationServer2012\Check\VpsProvisioningStatus\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('solidcpDebug');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/debug/vms/check_provisioning_status.html.twig', [
            'page_title' => 'Check VM',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create-vm-all-in-one", name=".createVmAllInOne")
     * @param Request $request
     * @param SOAPAllinOne\Create\VM\Handler $handler
     * @return Response
     */
    public function createVmAllInOne(Request $request, SOAPAllinOne\Create\VM\Handler $handler): Response
    {
        $command = new SOAPAllinOne\Create\VM\Command();

        $form = $this->createForm(SOAPAllinOne\Create\VM\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $result = $handler->handle($command);
                dump($result);
                return $this->redirectToRoute('solidcpDebug');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/solidcp/debug/all_in_one/create_vm.html.twig', [
            'page_title' => 'Create VM all in one',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }
}