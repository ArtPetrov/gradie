<?php

declare(strict_types=1);

namespace App\Controller\Dealer;

use App\Controller\ErrorHandler;
use App\Model\File\Service\Uploader;
use App\Model\Ticket\Entity\Message\Files;
use App\Model\Ticket\Entity\Ticket\Ticket;
use App\Model\Ticket\Repository\TicketRepository;
use App\Model\Ticket\UseCase\Ticket\Create;
use App\Model\Ticket\UseCase\Ticket\Ask;
use App\Model\Ticket\UseCase\Ticket\Read;
use App\Model\Ticket\UseCase\Ticket\Close;
use Knp\Component\Pager\PaginatorInterface;
use Nette\Utils\Strings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function list(TicketRepository $tickets, Request $request, PaginatorInterface $paginator)
    {

        $pagination = $paginator->paginate(
            $tickets->findForAuthor($this->getUser()),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('dealer/ticket/list.html.twig', [
            'tickets' => $pagination,
        ]);

    }

    /**
     * @Route("/ticket/add", name="ticket.add")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command($this->getUser());
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'ticket.created');
                return $this->redirectToRoute('dealer.tickets');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/ticket/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/ticket/{id}/closed", name="ticket.closed")
     * @IsGranted("TICKET_INTERACT", subject="ticket")
     */
    public function close(Ticket $ticket, Request $request, Close\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
            $this->addFlash('error', 'error.csrf');
            return $this->redirectToRoute('dealer.ticket', ['id' => $ticket->getId()]);
        }

        try {
            $handler->handle(Close\Command::forDealer($ticket, $this->getUser()));
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('dealer.ticket', ['id' => $ticket->getId()]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket")
     * @IsGranted("TICKET_INTERACT", subject="ticket")
     */
    public function ticket(Ticket $ticket, Request $request, Ask\Handler $handler, Read\Handler $handlerRead)
    {
        $handlerRead->handle(Read\Command::forDealer($ticket, $this->getUser()));

        $command = new Ask\Command($this->getUser(), $ticket);
        $form = $this->createForm(Ask\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('dealer.ticket', ['id' => $ticket->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/ticket/view.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ticket/file/{id}", name="ticket.link.file", methods={"GET"})
     * @IsGranted("TICKET_DOWNLOAD", subject="link")
     */
    public function download(Files $link, Uploader $uploader)
    {
        $file = $link->getFile();
        $response = new StreamedResponse(static function () use ($file, $uploader) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploader->read($file);
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $file->getMimeType());
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->getOriginalFilename(), Strings::toAscii($file->getFilename()));
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}
