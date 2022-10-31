<?php

declare(strict_types=1);

namespace App\Controller\Buyer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_BUYER")
 */
class CabinetController extends AbstractController
{
    /**
     * @Route("/",name="index")
     */
    public function main()
    {
        return $this->redirectToRoute('buyer.orders');
    }

    /**
     * @Route("/help",name="help")
     */
    public function help()
    {
        return $this->render('frontend/buyer/cabinet/help.html.twig');
    }
}
