<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\QuickOrder\UseCase\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuickOrderController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/quick-order/create/json",name="quick.order.create")
     */
    public function request(Request $request, Create\Handler $handler)
    {
        $name = $request->request->get('name');
        $contact = $request->request->get('contact');
        $product = $request->request->getInt('product', 0);
        $count = $request->request->getInt('count', 1);

        $command = Create\Command::fromFronted($product, $count, $name, $contact);
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json(['error' => $e->getMessage()], 406);
        }
        return $this->json('', 200);
    }
}
