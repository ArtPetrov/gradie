<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address;

use App\Model\Basket\Repository\BasketRepository;
use App\Model\Basket\Service\ReadBasketToken;
use App\Model\Flusher;
use App\Model\Order\Entity\Address;
use App\Model\Order\Entity\Status;
use App\Model\Order\Repository\OrderRepository;

class Handler
{
    private $flusher;
    private $token;
    private $basket;
    private $orders;

    public function __construct(Flusher $flusher, ReadBasketToken $token, BasketRepository $basket, OrderRepository $orders)
    {
        $this->flusher = $flusher;
        $this->token = $token;
        $this->basket = $basket;
        $this->orders = $orders;
    }

    public function handle(Command $command): void
    {
        $baseNamespace = 'App\Model\Order\UseCase\Ordering\Address\Cities\\' . $command->{$command->type}::TYPE;
        $cityHandler = $baseNamespace . '\Handler';
        $handler = new $cityHandler();
        $fields = $handler->handle($command->{$command->type});
        $address = new Address($command->type, $fields['city'], $fields);
        $order = $this->orders->findByUuid($command->order);
        $order->changeAddress($address);
        $order->changeStatus(new Status(Status::CLIENT_ENTERED_ADDRESS));
        $this->flusher->flush($order);
    }
}
