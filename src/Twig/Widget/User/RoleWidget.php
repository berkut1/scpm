<?php

declare(strict_types=1);

namespace App\Twig\Widget\User;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoleWidget extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_role', $this->role(...), ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function role(Environment $twig, string $role): string
    {
        return $twig->render('widget/user/role.html.twig', [
            'role' => $role,
        ]);
    }
}
