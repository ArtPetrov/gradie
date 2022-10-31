<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WorkSliderWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('slider_works', [$this, 'slider'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function slider(Environment $twig, ?array $images, ?string $tag='images'): string
    {
        return $twig->render('widget/frontend/slider_works.html.twig', [
            'images' => $images,
            'tag' => $tag
        ]);
    }
}
