<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Cpanel\Entity\Administrator;
use App\Model\Cpanel\Repository\AdministratorRepository;
use App\Model\Cpanel\UseCase\Administrator\Edit;
use App\Model\Cpanel\UseCase\Administrator\Create;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @IsGranted("ROLE_ROOT")
 */
class ProfileController extends AbstractController
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
     * @Route("/profile", name="profiles")
     */
    public function profiles(Request $request, AdministratorRepository $administrators, PaginatorInterface $paginator, CsrfTokenManagerInterface $csrfToken)
    {
        $pagination = $paginator->paginate(
            $administrators->findBy([], ['id' => 'DESC']),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/profile/list.html.twig', [
            'pagination' => $pagination,
            'token_csrf' => $csrfToken->getToken('delete'),
        ]);
    }

    /**
     * @Route("/profile/add", name="profile.add")
     */
    public function add(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'administrator.created');
                return $this->redirectToRoute('cpanel.profiles');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/profile/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile", methods={"POST","GET"})
     */
    public function edit(Administrator $administrator, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromAdministrator($administrator);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'administrator.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }

        }

        return $this->render('cpanel/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile.delete", methods={"DELETE"})
     */
    public function delete(Administrator $administrator, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($administrator);
        $em->flush();
        return $this->json(null, 204);
    }

}
