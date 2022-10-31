<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Review\UseCase\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/review/create/json",name="review.create")
     */
    public function request(Request $request, Create\Buyer\Handler $handler)
    {
        $name = $request->request->get('name','');
        $product = $request->request->getInt('product',0);
        $rating = $request->request->getInt('rating',5);
        $message = $request->request->get('message','');
        if ($this->getUser()) {
            $command = Create\Buyer\Command::formBuyer($this->getUser(),$product,$rating,$message);
        } else {
            $command = Create\Buyer\Command::fromGuest($product, $name, $rating, $message);
        }
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json(['error' => $e->getMessage()], 406);
        }
        return $this->json('', 200);
    }
}
