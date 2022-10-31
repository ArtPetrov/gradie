<?php

declare(strict_types=1);

namespace App\Twig\Dealer;

use App\Model\Cpanel\Repository\DealerRepository;
use App\Model\Salon\Repository\ModerationSalonRepository;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CountWaitWidget extends AbstractExtension
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dealer_count_moderation', [$this, 'countDealer'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('dealer_count_all', [$this, 'countAll'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('salon_count_moderation', [$this, 'countSalon'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function countAll(Environment $twig): string
    {
        $dealer = $this->container->get(DealerRepository::class)->countWaitDealers();
        $salon = $this->container->get(ModerationSalonRepository::class)->countRequest();
        return $twig->render('widget/dealer/count_wait.html.twig', [
            'count' => $dealer + $salon
        ]);
    }

    public function countSalon(Environment $twig): string
    {
        $count = $this->container->get(ModerationSalonRepository::class)->countRequest();
        return $twig->render('widget/dealer/count_wait.html.twig', [
            'count' => $count
        ]);
    }

    public function countDealer(Environment $twig): string
    {
        $count = $this->container->get(DealerRepository::class)->countWaitDealers();
        return $twig->render('widget/dealer/count_wait.html.twig', [
            'count' => $count
        ]);
    }
}
