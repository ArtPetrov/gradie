<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Cpanel\UseCase\Dealer\Moderation;
use App\Model\Cpanel\UseCase\Filter\Dealers;
use App\Model\Cpanel\UseCase\Dealer\Edit;
use App\Model\Cpanel\UseCase\Dealer\Create;
use App\Model\Cpanel\UseCase\Dealer\Block;
use App\Model\Cpanel\UseCase\Dealer\Unblock;
use App\Model\Cpanel\UseCase\Dealer\Activate;
use App\Model\Dealer\Entity\Dealer;
use App\Model\Cpanel\Repository\DealerRepository;
use App\Model\Dealer\ReadModel\DealerFetcher;
use App\Model\Salon\ReadModel\SalonFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ROOT")
 */
class DealersController extends AbstractController
{

    private const PER_PAGE = 20;

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/dealers", name="dealers")
     */
    public function dealers(Request $request, DealerRepository $dealers, Dealers\Filter $filter)
    {
        $form = $this->createForm(Dealers\Form::class, $filter);
        $form->handleRequest($request);

        $moderation = $this->createForm(Moderation\Form::class);

        $pagination = $dealers->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/dealers/list.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
            'formModeration' => $moderation->createView(),

        ]);
    }

    /**
     * @Route("/dealer/add", name="dealer.add")
     */
    public function add(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'dealer.created');
                return $this->redirectToRoute('cpanel.dealers');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }


        }
        return $this->render('cpanel/dealers/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dealer/{id}", name="dealer.edit", methods={"POST","GET"})
     */
    public function edit(Dealer $dealer, Request $request, SalonFetcher $salons, Edit\Handler $handler)
    {
        $command = Edit\Command::fromDealer($dealer);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'dealer.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/dealers/edit.html.twig', [
            'form' => $form->createView(),
            'dealer' => $dealer,
            'salons' => $salons->getSalonsWithOutDealer()
        ]);
    }

    /**
     * @Route("/dealer/{id}", name="dealer.delete", methods={"DELETE"})
     */
    public function delete(Dealer $dealer, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($dealer);
        $em->flush();
        return $this->json(null, 204);
    }

    /**
     * @Route("/dealer/{id}/block", name="dealer.block", methods={"POST"})
     */
    public function block(Dealer $dealer, Request $request, Block\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(new Block\Command($dealer));
        $this->addFlash('success', 'dealer.blocked');
        return $this->redirectToRoute('cpanel.dealer.edit', ['id' => $dealer->getId()]);
    }

    /**
     * @Route("/dealer/{id}/block/api", name="dealer.block.api", methods={"POST"})
     */
    public function blockApi(Dealer $dealer, Request $request, Block\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(new Block\Command($dealer));
        return $this->json(null, 204);
    }

    /**
     * @Route("/dealer/{id}/activate/api", name="dealer.activate.api", methods={"POST"})
     */
    public function activateApi(Dealer $dealer, Request $request, Activate\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Activate\Command($dealer);
        $command->sendMail = true;
        $command->generatePassword = true;
        $command->category = $request->request->get('category', null);
        $command->manager = $request->request->get('manager', null);
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/dealer/{id}/activate", name="dealer.activate", methods={"POST"})
     */
    public function activate(Dealer $dealer, Request $request, Activate\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Activate\Command($dealer);
        $command->sendMail = true;
        $command->generatePassword = true;
        $command->category = $request->request->get('category', null);
        $command->manager = $request->request->get('manager', null);
        $handler->handle($command);
        $this->addFlash('success', 'dealer.active');
        return $this->redirectToRoute('cpanel.dealer.edit', ['id' => $dealer->getId()]);
    }


    /**
     * @Route("/dealer/{id}/unblock", name="dealer.unblock", methods={"POST"})
     */
    public function unblock(Dealer $dealer, Request $request, Unblock\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(new Unblock\Command($dealer));
        $this->addFlash('success', 'dealer.unblock');
        return $this->redirectToRoute('cpanel.dealer.edit', ['id' => $dealer->getId()]);
    }

    /**
     * @Route("/dealers/search", name="dealers.search")
     */
    public function search(Request $request, DealerFetcher $dealers)
    {
        $querySearch = $request->request->get('query', '');
        return $this->json($dealers->searchByQuery($querySearch));
    }

}
