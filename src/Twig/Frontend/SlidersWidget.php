<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Slider\Entity\Type;
use App\Model\Slider\ReadModel\SlidersFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SlidersWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('slider_index', [$this, 'slidersIndex'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('slider_context', [$this, 'slidersContext'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('slider_index_context', [$this, 'slidersIndexContext'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function slidersIndex(Environment $twig): string
    {
        $sliders = $this->container->get(SlidersFetcher::class);
        return $twig->render('widget/frontend/slider_index.html.twig', [
            'sliders' => $sliders->getAllByTypeEnable(new Type(Type::INDEX))
        ]);
    }

    public function slidersIndexContext(Environment $twig): string
    {
        $sliders = $this->container->get(SlidersFetcher::class);
        return $twig->render('widget/frontend/slider_index_context.html.twig', [
            'sliders' => $sliders->getAllByTypeEnable(new Type(Type::CONTEXT))
        ]);
    }

    public function slidersContext(Environment $twig, int $count = 2): string
    {
        $sliders = $this->container->get(SlidersFetcher::class);
        return $twig->render('widget/frontend/slider_context.html.twig', [
            'sliders' => $sliders->getRandomContext($count)
        ]);
    }
}
