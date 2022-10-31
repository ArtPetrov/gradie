<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Copy;

use App\Model\Basket\UseCase\Add;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;
use App\Model\Order\Entity\Address;
use App\Model\Order\Entity\Order;
use App\Model\Order\Entity\Promocode;
use App\Model\Order\Entity\Status;
use App\Model\Order\Repository\OrderRepository;

class Handler
{
    private $orders;
    private $baskets;
    private $flusher;
    private $products;
    private $addInBasket;
    private $canceledOrder;

    public function __construct(
        OrderRepository $orders,
        ProductRepository $products,
        BasketRepository $baskets,
        Flusher $flusher,
        Add\Handler $addInBasket,
        \App\Model\Order\UseCase\Canceled\Order\Handler $canceledOrder

    )
    {
        $this->orders = $orders;
        $this->baskets = $baskets;
        $this->flusher = $flusher;
        $this->products = $products;
        $this->addInBasket = $addInBasket;
        $this->canceledOrder = $canceledOrder;
    }

    public function handle(Command $command): Order
    {
        $originOrder = $this->orders->get($command->order);
        $this->baskets->removeByToken($originOrder->getBasket());

        $newOrder = Order::create($originOrder->getContact(), $originOrder->getBasket());
        $newOrder->changeStatus(new Status(Status::CLIENT_CHOSE_HELP_MANAGER));

        foreach ($originOrder->getProducts() as $product) {
            $this->addInBasket->handle(
                new Add\Command(
                    $originOrder->getBasket(),
                    (int)$product->getProductId(),
                    $product->getCount())
            );
        }

        if ($originOrder->getPromocode()->getCode()) {
            $newOrder->updatePromocode(Promocode::createCopy($originOrder->getPromocode()));
        }

        $newOrder->changeAddress(
            new Address(
                $originOrder->getAddress()->getType(),
                $originOrder->getAddress()->getCity(),
                $originOrder->getAddress()->getFields(),
            ));

        $newOrder->editManagerComment($originOrder->getManagerComment());

        $this->orders->add($newOrder);
        $this->flusher->flush();

        if ($originOrder->getStatus()->canBeClosed()) {
            $this->canceledOrder->handle(\App\Model\Order\UseCase\Canceled\Order\Command::byOrder($originOrder));
        }

        return $newOrder;
    }
}
