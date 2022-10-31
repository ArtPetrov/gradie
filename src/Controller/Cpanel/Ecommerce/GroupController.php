<?php

declare(strict_types=1);

namespace App\Controller\Cpanel\Ecommerce;

use App\Controller\ErrorHandler;
use App\Model\Ecommerce\Entity\Group\Group;
use App\Model\Ecommerce\ReadModel\Group\GroupFetcher;
use App\Model\Ecommerce\UseCase\Group\Search;
use App\Model\Ecommerce\UseCase\Group\Edit;
use App\Model\Ecommerce\UseCase\Group\Remove;
use App\Model\Ecommerce\UseCase\Group\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    private const PER_PAGE = 15;

    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/groups", name="groups")
     */
    public function list(Request $request, GroupFetcher $groups)
    {
        $command = new Search\Command();
        $form = $this->createForm(Search\Form::class, $command);
        $form->handleRequest($request);
        return $this->render('ecommerce/group/list.html.twig', [
            'groups' => $groups->findByName($command->query, $request->query->getInt('page', 1), self::PER_PAGE),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/group/{id}/remove", name="group.remove")
     */
    public function remove(Group $group, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        try {
            $handler->handle(Remove\Command::fromGroup($group));
            return $this->json(null, 204);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
        }
        return $this->json(null, 200);
    }

    /**
     * @Route("/group/create", name="group.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'product.group.created');
                return $this->redirectToRoute('ecommerce.groups');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/group/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/group/{id}", name="group")
     */
    public function group(Group $group, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromGroup($group);
        $form = $this->createForm(Edit\Form::class, $command);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'product.group.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/group/view.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
}
