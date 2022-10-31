<?php

declare(strict_types=1);

namespace App\Controller\Dealer;

use App\Controller\ErrorHandler;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\ReadModel\SalonFetcher;
use App\Model\Salon\Repository\ModerationSalonRepository;
use App\Model\Salon\UseCase\Dealer\Edit;
use App\Model\Salon\UseCase\Dealer\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
            $salons->getSalonsForDealer($this->getUser()),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('dealer/salon/list.html.twig', [
            'salons' => $pagination
        ]);
    }

    /**
     * @Route("/salon/{id}", name="salon", methods={"POST","GET"})
     * @IsGranted("SALON_NONE_PROCESS_DELETE", subject="salon")
     * @IsGranted("SALON_OWNER", subject="salon")
     */
    public function edit(Salon $salon, ModerationSalonRepository $moderation, Request $request, Edit\Handler $handler)
    {
        $moderationInfo = $moderation->getLastRequestInProcessOfSalon($salon);
        $command = Edit\Command::fromSalon($salon, $moderationInfo, $this->getUser());

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'salon.updated');
                return $this->redirectToRoute('dealer.salons');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/salon/edit.html.twig', [
            'form' => $form->createView(),
            'salon' => $salon,
            'moderation' => !is_null($moderationInfo)
        ]);
    }

    /**
     * @Route("/salon/{id}/remove", name="salon.remove")
     * @IsGranted("SALON_OWNER", subject="salon")
     */
    public function delete(Salon $salon, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($this->getUser(), $salon);
        $handler->handle($command);
        return $this->json(null, 204);
    }

}
