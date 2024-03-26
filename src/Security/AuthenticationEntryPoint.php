<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final readonly class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator) {}

    #[\Override]
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        // add a custom flash message and redirect to the login page
        $request->getSession()->getFlashBag()->add('note', 'You have to login in order to access this page.');

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}