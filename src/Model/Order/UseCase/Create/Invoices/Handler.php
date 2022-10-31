<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Create\Invoices;

use App\Model\Flusher;
use App\Model\Order\Entity\Invoice\Invoice;
use App\Model\Order\Entity\Invoice\Type;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Order\UseCase\Ordering\Address;
use App\Model\Promocode\Repository\PromocodeRepository;

class Handler
{
    private $flusher;
    private $orders;
    private $promocodes;
    private $shippingPrice;


    public function __construct(
        Flusher $flusher,
        PromocodeRepository $promocodes,
        OrderRepository $orders,
        Address\ShippingPrice $shippingPrice
    )
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->promocodes = $promocodes;
        $this->shippingPrice = $shippingPrice;
    }

    public function handle(Command $command): void
    {
        $order = $this->orders->getByUuid($command->order);

        $costProducts = 0;
        /* @var \App\Model\Order\Entity\Product $product */
        foreach ($order->getProducts() as $product) {
            $costProducts += $product->getPrice() * $product->getCount();
        }

        $invoiceMain = Invoice::create($order, new Type(Type::MAIN), $costProducts);
        $costWithDiscount = $costProducts;
        if ($code = $order->getPromocode()->getCode()) {
            try {
                $promo = $this->promocodes->getByCode($code);
                $discount = $promo->getDiscount($costProducts);
                $costWithDiscount = $costProducts - $discount;
                $invoiceMain = Invoice::create($order, new Type(Type::MAIN), $costWithDiscount, 'Promocode discount: ' . $discount);

            } catch (\DomainException $exception) {
            }
        }
        $order->createInvoice($invoiceMain);

        if ($order->getAddress()->getType()) {
            $shippingCost = $this->shippingPrice->calculate(Address\Command::fromOrderWithOrderPrice($order, $costProducts, $costWithDiscount));
            if($shippingCost>0){
                $order->createInvoice(Invoice::create($order, new Type(Type::SHIPPING), $shippingCost));
            }
        }

        $this->flusher->flush($order);
    }
}
