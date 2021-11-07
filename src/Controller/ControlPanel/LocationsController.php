<?php 
declare(strict_types=1);

namespace App\Controller\ControlPanel;

use App\Model\ControlPanel\Entity\Location\Location;
use App\Model\ControlPanel\UseCase\Location\Create;
use App\Model\ControlPanel\UseCase\Location\Edit;
use App\Model\ControlPanel\UseCase\Location\Remove;
use App\ReadModel\ControlPanel\Location\LocationFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/locations", name="locations")
 * @IsGranted("ROLE_MODERATOR")
 */
class LocationsController extends AbstractController
{
    private const PER_PAGE = 25;
    private const MAIN_TITLE = 'Locations';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @param LocationFetcher $fetcher
     * @return Response
     */
    public function index(Request $request, LocationFetcher $fetcher): Response
    {

        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/control_panel/locations/index.html.twig', [
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
                return $this->redirectToRoute('locations');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/control_panel/locations/create.html.twig', [
            'page_title' => 'Add Location',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     * @param Location $location
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    public function edit(Location $location, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromLocation($location);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('locations');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/control_panel/locations/edit.html.twig', [
            'page_title' => 'Edit Location',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name=".remove", methods={"POST"})
     * @param Location $location
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    public function remove(Location $location, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('locations');
        }

        $command = new Remove\Command($location->getId());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('locations');
    }
}