<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FiltersWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('filter_selector_color', [$this, 'getFilterColorGroup'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('filter_default', [$this, 'getDefault'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function getDefault(Environment $twig, $filter): string
    {
        return $twig->render('widget/frontend/default_filters.html.twig', ['filter' => $filter]);
    }

    public function getFilterColorGroup(Environment $twig, array $filter): string
    {
        return $twig->render('widget/frontend/color_filters.html.twig', ['filter' => $filter['slug'], 'label' => $filter['label'], 'choose' => $filter['choose'] ? $filter['choose']['value'] : null]);
    }
}
