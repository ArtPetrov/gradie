<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Cpanel\Entity\Manager;
use App\Model\Cpanel\Repository\ManagerRepository;
use App\Model\Cpanel\UseCase\Manager\Edit;
use App\Model\Cpanel\UseCase\Manager\Create;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @IsGranted("ROLE_ROOT")
 */
class ManagerController extends AbstractController
{

    private const PER_PAGE = 15;

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/manager", name="managers")
     */
    public function managers(Request $request, ManagerRepository $managers, PaginatorInterface $paginator, CsrfTokenManagerInterface $csrfToken)
    {
        $pagination = $paginator->paginate(
            $managers->findBy([], ['id' => 'DESC']),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/manager/list.html.twig', [
            'pagination' => $pagination,
            'token_csrf' => $csrfToken->getToken('delete'),
        ]);
    }

    /**
     * @Route("/manager/add", name="manager.add")
     */
    public function add(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'manager.created');
                return $this->redirectToRoute('cpanel.managers');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/manager/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manager/{id}", name="manager", methods={"POST","GET"})
     */
    public function edit(Manager $manager, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromManager($manager);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'manager.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/manager/edit.html.twig', [
            'form' => $form->createView(),
            'manager'=> $manager,
        ]);
    }

    /**
     * @Route("/manager/{id}", name="manager.delete", methods={"DELETE"})
     */
    public function delete(Manager $manager, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($manager);
        $em->flush();
        return $this->json(null, 204);
    }

}
