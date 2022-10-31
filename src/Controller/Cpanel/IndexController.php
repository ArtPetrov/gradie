<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Model\File\UseCase\Add;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

class IndexController extends AbstractController
{
    /**
     * @Route("/",name="index")
     */
    public function index()
    {
        return $this->redirectToRoute('cpanel.orders');
    }

}