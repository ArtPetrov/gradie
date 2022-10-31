<?php

declare(strict_types=1);

namespace App\Model\Order\Service;

use App\Helper\BasketTokenInterface;
use App\Model\Basket\Entity\Item;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Basket\Service\ReadGuestToken;
use App\Model\Flusher;
use App\Model\Order\Entity\Order;

class TransferProductsFromBasket
{
    private $basket;
    /**
     * @var Flusher
     */
    private $flusher;
    /**
     * @var BasketTokenInterface|null
     */
    private $guestToken;

    public function __construct(
        BasketRepository $basket,
        Flusher $flusher,
        ReadGuestToken $guestToken
    )
    {
        $this->basket = $basket;
        $this->flusher = $flusher;
        $this->guestToken = $guestToken->getToken();
    }

    public function inOrder(BasketTokenInterface $token, Order $order): void
    {
        $productsInBasket = $this->basket->findAllByToken($token);
        $isUser = $token->getToken() !== $this->guestToken->getToken();
        /** @var Item $item */
        foreach ($productsInBasket as $item) {
            $order->addProduct($item->getProduct(), $item->getCount());
            $this->basket->removeByTokenAndProduct($token, $item->getProduct());
            if ($isUser && $this->guestToken) {
                $this->basket->removeByTokenAndProduct($this->guestToken, $item->getProduct());
            }
        }
        $this->flusher->flush($order);
    }
}

