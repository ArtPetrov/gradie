<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Slider\Entity\Type;
use App\Model\Slider\Entity\Slider;
use App\Model\Slider\ReadModel\SlidersFetcher;
use App\Model\Slider\UseCase\Move;
use App\Model\Slider\UseCase\Edit;
use App\Model\Slider\UseCase\Create;
use App\Model\Slider\UseCase\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SlidersController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/sliders/{type}", name="sliders", requirements={"type"="(index|context)"})
     */
    public function list(string $type, Request $request, SlidersFetcher $sliders, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $sliders->getAllByType(new Type($type)),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/sliders/list.html.twig', [
            'sliders' => $pagination
        ]);
    }

    /**
     * @Route("/sliders/{type}/create", name="slider.create", requirements={"type"="(index|context)"})
     */
    public function create(string $type, Request $request, Create\Handler $handler)
    {
        $command = new Create\Command(new Type($type));
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'slider.created');
                return $this->redirectToRoute('cpanel.sliders', ['type' => $type]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/sliders/create.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    /**
     * @Route("/slider/{id}", name="slider", methods={"POST","GET"})
     */
    public function edit(Slider $slider, Request $request, Edit\Handler $handler)
    {
        $command = new Edit\Command($slider);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'slider.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/sliders/edit.html.twig', [
            'type' => $slider->getType()->getType(),
            'form' => $form->createView(),
            'slider'=> $slider
        ]);
    }

    /**
     * @Route("/slider/{id}/remove", name="slider.remove")
     */
    public function delete(Slider $slider, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($slider->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/slider/{id}/position", name="slider.position")
     */
    public function position(Slider $slider, Request $request, Move\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Move\Command($slider->getId(), $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

}
