<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Lead\Entity\Lead;
use App\Model\Lead\Repository\LeadRepository;
use App\Model\Lead\UseCase\Remove;
use App\Model\Lead\UseCase\Edit;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LeadController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/request-prices", name="leads")
     */
    public function list(Request $request, LeadRepository $leads, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $leads->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/lead/list.html.twig', [
            'leads' => $pagination
        ]);
    }

    /**
     * @Route("/request-price/{id}", name="lead", methods={"POST","GET"})
     */
    public function edit(Lead $lead, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromLead($lead);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);
        $handler->handle($command);
        return $this->render('cpanel/lead/edit.html.twig', [
            'form' => $form->createView(),
            'lead'=> $lead,
        ]);
    }

    /**
     * @Route("/request-price/{id}/remove", name="lead.remove")
     */
    public function delete(Lead $lead, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($lead->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

}
