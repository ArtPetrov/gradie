<?php

declare(strict_types=1);

namespace App\Twig\Cpanel;

use App\Model\DesignProject\Repository\ProjectRepository;
use App\Model\Lead\Repository\LeadRepository;
use App\Model\Order\Repository\OrderRepository;
use App\Model\QuickOrder\Repository\QuickOrderRepository;
use App\Model\Quiz\Repository\ResultRepository;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationsWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('all_notifications', [$this, 'countAll'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('quick_order_new', [$this, 'countQuickOrders'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('orders_new', [$this, 'countOrders'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('quiz_new', [$this, 'countQuiz'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function countAll(Environment $twig): string
    {
        $count = 0;
        $count += $this->container->get(LeadRepository::class)->countNew();
        $count += $this->container->get(ProjectRepository::class)->countNew();
        $count += $this->container->get(QuickOrderRepository::class)->countNew();
        $count += $this->container->get(OrderRepository::class)->countNew();
        $count += $this->container->get(ResultRepository::class)->countNew();

        return $twig->render('widget/cpanel/count.html.twig', [
            'count' => $count
        ]);
    }

    public function countQuickOrders(Environment $twig): string
    {
        $count = $this->container->get(QuickOrderRepository::class)->countNew();
        return $twig->render('widget/cpanel/count.html.twig', [
            'count' => $count
        ]);
    }

    public function countOrders(Environment $twig): string
    {
        $count = $this->container->get(OrderRepository::class)->countNew();
        return $twig->render('widget/cpanel/count.html.twig', [
            'count' => $count
        ]);
    }

    public function countQuiz(Environment $twig): string
    {
        $count = $this->container->get(ResultRepository::class)->countNew();
        return $twig->render('widget/cpanel/count.html.twig', [
            'count' => $count
        ]);
    }
}
