<?php

declare(strict_types=1);

namespace App\Twig;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GetVarWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getParam', [$this, 'getParam'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }

    public function getParam(string $param)
    {
        return $this->container->getParameter($param);
    }
}
