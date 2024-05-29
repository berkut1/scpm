<?php

declare(strict_types=1);

namespace App\Twig\Widget\User;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class StatusWidget extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_status', $this->status(...), ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function status(Environment $twig, string $status): string
    {
        return $twig->render('widget/user/status.html.twig', [
            'status' => $status,
        ]);
    }
}
