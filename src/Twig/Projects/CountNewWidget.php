<?php

declare(strict_types=1);

namespace App\Twig\Projects;

use App\Model\DesignProject\Repository\ProjectRepository;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CountNewWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('design_project_new', [$this, 'countNew'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function countNew(Environment $twig): string
    {
        $count = $this->container->get(ProjectRepository::class)->countNew();
        return $twig->render('widget/project/count_new.html.twig', [
            'count' => $count
        ]);
    }
}
