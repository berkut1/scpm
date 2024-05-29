<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\{Activate, Create, Password, Role, Suspend};
use App\Model\User\UseCase\Remove\Archive;
use App\ReadModel\User\Filter;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users', name: 'users')]
#[IsGranted('ROLE_ADMIN')]
final class UsersController extends AbstractController
{
    private const int PER_PAGE = 10;
    private const string MAIN_TITLE = 'Users';

    public function __construct(private readonly LoggerInterface $logger) {}

    #[Route('', name: '')]
    public function index(Request $request, UserFetcher $fetcher): Response
    {
        $filter = new Filter\Filter();

        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/users/index.html.twig', [
            'page_title' => self::MAIN_TITLE,
            'pagination' => $pagination,
            'form' => $form->createView(),
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
                return $this->redirectToRoute('users');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/create.html.twig', [
            'page_title' => 'Add User',
            'main_title' => self::MAIN_TITLE,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/password', name: '.password', requirements: ['id' => Requirement::UID_RFC4122])]
    public function password(User $user, Request $request, Password\Handler $handler): Response
    {
        $command = new Password\Command($user->getId()->getValue());

        $form = $this->createForm(Password\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('app/users/password.html.twig', [
            'page_title' => 'Change password',
            'main_title' => self::MAIN_TITLE,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/role', name: '.role', requirements: ['id' => Requirement::UID_RFC4122])]
    #[IsGranted('ROLE_ADMIN')]
    public function role(User $user, Request $request, Role\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to change role for yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = Role\Command::fromUser($user);

        $form = $this->createForm(Role\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/role.html.twig', [
            'page_title' => 'Change role',
            'main_title' => self::MAIN_TITLE,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/activate', name: '.activate', requirements: ['id' => Requirement::UID_RFC4122], methods: ['POST'])]
    public function activate(User $user, Request $request, Activate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('activate', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Activate\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/suspend', name: '.suspend', requirements: ['id' => Requirement::UID_RFC4122], methods: ['POST'])]
    public function suspend(User $user, Request $request, Suspend\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('suspend', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to suspend yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Suspend\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/remove', name: '.remove', requirements: ['id' => Requirement::UID_RFC4122], methods: ['POST'])]
    public function remove(User $user, Request $request, Archive\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to remove yourself.');
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Archive\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users', ['id' => $user->getId()]);
    }

    #[Route('/{id}', name: '.show', requirements: ['id' => Requirement::UID_RFC4122])]
    public function show(User $user): Response
    {
        return $this->render('app/users/show.html.twig', [
            'main_title' => self::MAIN_TITLE,
            'user' => $user,
        ]);
    }
}
