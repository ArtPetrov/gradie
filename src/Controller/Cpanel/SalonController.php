<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;

use App\Model\Salon\Entity\Owners;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\ReadModel\SalonFetcher;
use App\Model\Salon\Repository\ModerationSalonRepository;
use App\Model\Salon\UseCase\Create;
use App\Model\Salon\UseCase\Edit;
use App\Model\Salon\UseCase\Dealer;
use App\Model\Salon\UseCase\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SalonController extends AbstractController
{

    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/salons", name="salons")
     */
    public function list(Request $request, SalonFetcher $salons, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $salons->getSalonsWithModeration(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/salon/list.html.twig', [
            'salons' => $pagination
        ]);
    }

    /**
     * @Route("/salon/add", name="salon.add")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'salon.append');
                return $this->redirectToRoute('cpanel.salons');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/salon/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/salon/{id}", name="salon", methods={"POST","GET"})
     */
    public function edit(Salon $salon, ModerationSalonRepository $moderations, Request $request, Edit\Handler $handler)
    {
        $moderation = $moderations->getLastRequestInProcessOfSalon($salon);
        $command = Edit\Command::fromSalon($salon,$moderation);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'salon.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        $moderation = $moderations->getLastRequestInProcessOfSalon($salon);

        return $this->render('cpanel/salon/edit.html.twig', [
            'form' => $form->createView(),
            'salon' => $salon,
            'moderation' => $moderation
        ]);
    }

    /**
     * @Route("/salon/{id}/remove", name="salon.remove")
     */
    public function delete(Salon $salon, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($salon->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/salon/{id}/cancel/remove", name="cpanel.salon.cancel.remove")
     */
    public function cancelRemove(Salon $salon, Dealer\Cancel\Remove\Handler $handler)
    {
        $command = new Dealer\Cancel\Remove\Command($salon->getId());
        $handler->handle($command);
        $this->addFlash('success', 'salon.request.cancel');
        return $this->redirectToRoute('cpanel.salons');
    }

    /**
     * @Route("/salon/dealer/{id}/remove", name="salon.remove.dealer")
     */
    public function removeDealer(Owners $owner, Request $request, Dealer\Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Dealer\Remove\Command($owner->getDealer(), $owner->getSalon(),);
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/salon/{salon}/assign-dealer/{dealer}", name="salon.assign.dealer")
     */
    public function assignDealer(int $salon, int $dealer, Request $request, Dealer\Assign\Handler $handler)
    {
        try {
            $command = new Dealer\Assign\Command($dealer, $salon);
            $handler->handle($command);
            $this->addFlash('success', 'dealer.assign.salon');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('cpanel.dealer.edit',['id'=>$dealer]);
    }
}
