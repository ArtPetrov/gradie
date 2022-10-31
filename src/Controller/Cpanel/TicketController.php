<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Ticket\Entity\Ticket\Status;
use App\Model\Ticket\Entity\Ticket\Ticket;
use App\Model\Ticket\Repository\TicketRepository;
use App\Model\Ticket\UseCase\Ticket\Reply;
use App\Model\Ticket\UseCase\Ticket\Read;
use App\Model\Ticket\UseCase\Ticket\Close;
use App\Model\Ticket\UseCase\Ticket\Delete;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    private const PER_PAGE = 10;

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/tickets", name="tickets")
     */
    public function listActive(TicketRepository $tickets, Request $request, PaginatorInterface $paginator)
    {

        $pagination = $paginator->paginate(
            $tickets->findbyStatus(Status::OPEN),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/ticket/list.html.twig', [
            'tickets' => $pagination,
        ]);

    }

    /**
     * @Route("/tickets/closed", name="tickets.closed")
     */
    public function listClosed(TicketRepository $tickets, Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $tickets->findbyStatus(Status::CLOSED),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/ticket/list.html.twig', [
            'tickets' => $pagination,
        ]);

    }

    /**
     * @Route("/ticket/{id}/closed", name="ticket.closed")
     */
    public function close(Ticket $ticket, Request $request, Close\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
            $this->addFlash('error', 'error.csrf');
            return $this->redirectToRoute('cpanel.ticket', ['id' => $ticket->getId()]);
        }

        try {
            $handler->handle(Close\Command::forSupport($ticket, $this->getUser()));
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('cpanel.ticket', ['id' => $ticket->getId()]);
    }

    /**
     * @Route("/ticket/{id}/delete", name="ticket.delete")
     */
    public function delete(Ticket $ticket, Request $request, Delete\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
            $this->addFlash('error', 'error.csrf');
            return $this->redirectToRoute('cpanel.ticket', ['id' => $ticket->getId()]);
        }

        try {
            $handler->handle(new Delete\Command($ticket));
            return $this->redirectToRoute('cpanel.tickets');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('cpanel.ticket', ['id' => $ticket->getId()]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket")
     */
    public function ticket(Ticket $ticket, Request $request, Reply\Handler $handler, Read\Handler $handlerRead)
    {
        $handlerRead->handle(Read\Command::forSupport($ticket, $this->getUser()));

        $command = new Reply\Command($this->getUser(), $ticket);
        $form = $this->createForm(Reply\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('cpanel.ticket', ['id' => $ticket->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/ticket/view.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }


}
