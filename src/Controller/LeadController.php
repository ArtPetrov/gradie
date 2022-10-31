<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Lead\Entity\Type;
use App\Model\Lead\UseCase\Create;
use App\Model\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeadController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/request/{slug}",name="request.price",requirements={"slug"="(company|client)"})
     */
    public function requestPrice($slug, PageRepository $pages, Request $request, Create\Handler $handler)
    {
        $page = $pages->getBySlug('request/' . $slug);
        $type = $slug === 'client' ? Type::CLIENT : Type::COMPANY;
        $command = new Create\Command($type);

        $makeRequest = false;

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($request->cookies->getBoolean($slug . 'completed', false) === true) {
            $makeRequest = true;
        }

        $response = new Response();

        if ($form->isSubmitted() && $form->isValid() && !$makeRequest) {
            try {
                $handler->handle($command);
                $makeRequest = true;
                $response->headers->setCookie(new Cookie($slug . 'completed', 'true', time() + 60 * 60 * 24 * 90));
            } catch (\DomainException $e) {
                $this->errors->handle($e);
            }
        }

        return $this->render('frontend/lead/request.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'form' => $form->createView(),
            'complete' => $makeRequest,
            'type' => $slug
        ], $response);

    }
}
