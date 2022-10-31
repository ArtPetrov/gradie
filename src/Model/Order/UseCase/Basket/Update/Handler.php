<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Basket\Update;

use App\Model\Basket\Repository\BasketRepository;
use App\Model\Order\UseCase\Canceled;
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
        $item = $this->basket->getByTokenAndProduct($command->basket, (int)$command->product);
        $item->updateCount((int)$command->count);
        $this->flusher->flush($item);
    }
}
