<?php

declare(strict_types=1);

namespace App\Twig\Cpanel;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EqualDataWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('not_equal_infromation', [$this, 'notEqual'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function notEqual(Environment $twig, ?string $current, ?string $moderation): string
    {
        return $twig->render('widget/cpanel/equal_data.html.twig', [
            'current' => $current,
            'moderation' => $moderation,
        ]);
    }
}
