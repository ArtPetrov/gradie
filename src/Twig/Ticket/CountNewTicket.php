<?php

declare(strict_types=1);

namespace App\Twig\Ticket;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Ticket\Repository\TicketRepository;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CountNewTicket extends AbstractExtension
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ticket_count_new', [$this, 'countNewTickets'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('ticket_count_new_for_dealer', [$this, 'newResponseForDealer'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function countNewTickets(Environment $twig): string
    {
        $count = $this->container->get(TicketRepository::class)->countNewForSupport();
        return $twig->render('widget/ticket/count_new.html.twig', [
            'count' => $count
        ]);
    }

    public function newResponseForDealer(Environment $twig, Dealer $dealer): string
    {
        $count = $this->container->get(TicketRepository::class)->countNewReplyForDealer($dealer);
        return $twig->render('widget/ticket/count_new.html.twig', [
            'count' => $count
        ]);
    }
}
