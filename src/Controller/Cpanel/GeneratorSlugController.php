<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Helper\GeneratorSlug;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GeneratorSlugController extends AbstractController
{
    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/generator-slug", name="generator.slug")
     */
    public function generator(Request $request, GeneratorSlug $slug)
    {
        $value = $request->request->get('value', 'none');
        return $this->json([
            'slug' => $slug->make($value)
        ]);
    }
}
