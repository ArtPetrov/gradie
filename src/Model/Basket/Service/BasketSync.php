<?php

declare(strict_types=1);

namespace App\Model\Basket\Service;

use App\Helper\BasketTokenInterface;
use App\Model\Basket\Entity\Item;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Basket\UseCase\Add;

class BasketSync
{
    private $basket;
    private $handler;

    public function __construct(BasketRepository $basket, Add\Handler $handler)
    {
        $this->basket = $basket;
        $this->handler = $handler;
    }

    public function searchDifficult(BasketTokenInterface $guest, BasketTokenInterface $buyer): void
    {
        if (!$guest->isAvailable() || !$buyer->isAvailable()) {
            return;
        }

        $questItems = $this->basket->findAllByToken($guest);
        if (0 === count($questItems)) {
            return;
        }

        $buyerItems = $this->basket->findAllByToken($buyer);

        /** @var Item $item */
        foreach ($questItems as $item) {
            /** @var Item $itemBuyer */
            foreach ($buyerItems as $itemBuyer) {
                if ($item->getProduct()->getId() === $itemBuyer->getProduct()->getId()) {
                    if ($item->getCount() <= $itemBuyer->getCount()) {
                        continue 2;
                    }
                    $itemBuyer->updateCount($item->getCount());
                }
            }
            $this->handler->handle(new Add\Command($buyer, $item->getProduct()->getId(), $item->getCount()));
        }
    }
}
