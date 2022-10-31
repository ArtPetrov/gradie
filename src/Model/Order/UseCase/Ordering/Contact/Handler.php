<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Contact;

use App\Model\Basket\Service\ReadBasketToken;
use App\Model\Flusher;
use App\Model\Order\Entity\Contact;
use App\Model\Order\Entity\Order;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Order\Service\CurrentOrder;
use App\Model\Promocode\Entity\Promocode;
use App\Model\Promocode\Repository\PromocodeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Handler
{
    private $flusher;
    private $orders;
    private $basketToken;
    /** @var Request|null */
    private $request;
    private $promocodes;

    public function __construct(
        Flusher $flusher,
        OrderRepository $orders,
        ReadBasketToken $basketToken,
        RequestStack $requestStack,
        PromocodeRepository $promocodes
    )
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->request = $requestStack->getCurrentRequest();
        $this->basketToken = $basketToken;
        $this->promocodes = $promocodes;
    }

    public function handle(Command $command): void
    {
        $contact = new Contact($command->name, $command->phone, $command->email);
        if ($command->uuid) {
            $order = $this->update($command, $contact);
        } else {
            $order = $this->create($command, $contact);
        }

        $promo = $this->request->getSession()->get(Promocode::NAME_SESSION);
        if ($promo && $order->getPromocode()->getCode() !== $promo) {
            if ($this->promocodes->hasCode($promo)) {
                $promocode = $this->promocodes->getByCode($promo);
                $order->updatePromocode(\App\Model\Order\Entity\Promocode::createFromPromocode($promocode));
            }
        }

        $this->flusher->flush($order);
        $this->request->getSession()->set(CurrentOrder::NAMING, $order->getUuid());
    }

    public function update(Command $command, Contact $contact): Order
    {
        $order = $this->orders->findByUuid($command->uuid);
        if (!$order) {
            return $this->create($command, $contact);
        }
        $order->updateContact($contact);
        return $order;
    }

    public function create(Command $command, Contact $contact): Order
    {
        $order = Order::create($contact, $this->basketToken->getToken());
        $this->orders->add($order);
        return $order;
    }
}
