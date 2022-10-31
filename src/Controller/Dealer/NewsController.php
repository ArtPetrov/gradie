<?php

declare(strict_types=1);

namespace App\Controller\Dealer;

use App\Model\News\Repository\NewsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    private const PER_PAGE = 5;

    /**
     * @Route("/", name="index")
     * @IsGranted("ROLE_DEALER")
     */
    public function index(Request $request, NewsRepository $newsRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $newsRepository->findActiveNews(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('dealer/news/list.html.twig', [
            'news' =>$pagination
        ]);
    }


}
