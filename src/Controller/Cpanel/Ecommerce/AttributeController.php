<?php

declare(strict_types=1);

namespace App\Controller\Cpanel\Ecommerce;

use App\Controller\ErrorHandler;
use App\Model\Ecommerce\Entity\Attribute\Attribute;
use App\Model\Ecommerce\ReadModel\Attribute\AttributeFetcher;
use App\Model\Ecommerce\Repository\AttributeRepository;
use App\Model\Ecommerce\UseCase\Attribute\Create;
use App\Model\Ecommerce\UseCase\Attribute\Delete;
use App\Model\Ecommerce\UseCase\Attribute\Edit;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AttributeController extends AbstractController
{
    private const PER_PAGE = 10;

    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/attributes", name="attributes")
     */
    public function list(AttributeRepository $attributes, Request $request, PaginatorInterface $paginator)
    {

        $pagination = $paginator->paginate(
            $attributes->sortByAlphabetically(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('ecommerce/attribute/list.html.twig', [
            'attributes' => $pagination,
        ]);

    }

    /**
     * @Route("/attributes/json", name="attributes.json")
     */
    public function listJson(AttributeFetcher $attributes)
    {
        return $this->json($attributes->all());
    }

    /**
     * @Route("/attribute/create", name="attribute.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'attribute.created');
                return $this->redirectToRoute('ecommerce.attributes');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/attribute/create.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/attribute/{id}/delete", name="attribute.delete")
     */
    public function delete(Attribute $attribute, Request $request, Delete\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        try {
            $handler->handle(new Delete\Command($attribute->getId()));
            return $this->json(null, 204);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
        }
        return $this->json(null, 200);
    }


    /**
     * @Route("/attribute/{id}", name="attribute")
     */
    public function attribute(Attribute $attribute, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromAttribute($attribute);
        $form = $this->createForm(Edit\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'attribute.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/attribute/view.html.twig', [
            'attribute' => $attribute,
            'form' => $form->createView(),
        ]);

    }


}
