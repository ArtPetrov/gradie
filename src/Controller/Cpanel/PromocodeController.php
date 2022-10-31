<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Promocode\Entity\Promocode;
use App\Model\Promocode\UseCase\Edit;
use App\Model\Promocode\UseCase\Remove;
use App\Model\Promocode\UseCase\Create;
use App\Model\Promocode\Repository\PromocodeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromocodeController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/promocodes", name="promocodes")
     */
    public function list(Request $request, PromocodeRepository $promocodes, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $promocodes->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/promocode/list.html.twig', [
            'promocodes' => $pagination
        ]);
    }

    /**
     * @Route("/promocode/create", name="promocode.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'promocode.created');
                return $this->redirectToRoute('cpanel.promocodes');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/promocode/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/promocode/{id}", name="promocode")
     */
    public function edit(Promocode $promocode, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromPromocode($promocode);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'promocode.update');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/promocode/edit.html.twig', [
            'form' => $form->createView(),
            'promo' => $promocode,
        ]);
    }

    /**
     * @Route("/promocode/{id}/remove", name="promocode.remove")
     */
    public function remove(Promocode $promocode, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($promocode->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }
}
