<?php

declare(strict_types=1);

namespace App\Model\Basket\UseCase\Update;

use App\Model\Basket\Entity\BasketToken;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $basket;

    public function __construct(Flusher $flusher, BasketRepository $basket)
    {
        $this->flusher = $flusher;
        $this->basket = $basket;
    }

    public function handle(Command $command): void
    {
        $item = $this->basket->getByTokenAndProduct(new BasketToken($command->token), $command->product);
        $item->updateCount($command->count);
        $this->flusher->flush($item);
    }
}
