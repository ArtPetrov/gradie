<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Buyer\Entity\Buyer;
use App\Model\Cpanel\UseCase\Filter\Buyers;
use App\Model\Buyer\UseCase\Remove;
use App\Model\Buyer\UseCase\Setting\Information;
use App\Model\Buyer\Repository\BuyerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ROOT")
 */
class BuyersController extends AbstractController
{

    private const PER_PAGE = 20;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/buyers", name="buyers")
     */
    public function buyers(Request $request, BuyerRepository $buyers, Buyers\Filter $filter)
    {
        $form = $this->createForm(Buyers\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $buyers->search(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/buyers/list.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/buyer/{id}", name="buyer.edit", methods={"POST","GET"})
     */
    public function edit(Buyer $buyer, Request $request, Information\Handler $handler)
    {
        $command = Information\Command::fromBuyer($buyer);

        $form = $this->createForm(Information\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'buyer.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/buyers/edit.html.twig', [
            'form' => $form->createView(),
            'buyer' => $buyer
        ]);
    }

    /**
     * @Route("/buyer/{id}", name="buyer.remove", methods={"DELETE"})
     */
    public function remove(Buyer $buyer, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($buyer->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }
}
