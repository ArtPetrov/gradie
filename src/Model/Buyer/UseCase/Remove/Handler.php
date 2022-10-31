<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Remove;

use App\Model\Basket\Repository\BasketRepository;
use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Flusher;

class Handler
{
    private $buyers;
    private $flusher;
    private $basket;

    public function __construct(BuyerRepository $buyers, Flusher $flusher, BasketRepository $basket)
    {
        $this->buyers = $buyers;
        $this->flusher = $flusher;
        $this->basket = $basket;
    }

    public function handle(Command $command): void
    {
        $buyer = $this->buyers->get($command->id);
        $this->buyers->remove($buyer);

        if ($buyer->getBasketToken()->isAvailable()) {
            $this->basket->removeByToken($buyer->getBasketToken());
        }

        $this->flusher->flush();
    }
}
