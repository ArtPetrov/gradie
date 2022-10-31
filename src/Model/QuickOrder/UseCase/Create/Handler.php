<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\UseCase\Create;

use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;
use App\Model\QuickOrder\Entity\Order;
use App\Model\QuickOrder\Entity\Product;
use App\Model\QuickOrder\Entity\Client;
use App\Model\QuickOrder\Repository\QuickOrderRepository;

class Handler
{
    private $flusher;
    private $products;
    private $orders;

    public function __construct(Flusher $flusher, QuickOrderRepository $orders, ProductRepository $products)
    {
        $this->flusher = $flusher;
        $this->products = $products;
        $this->orders = $orders;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get((int)$command->product);
        $productOrder = new Product($product->getId(), $product->getInfo()->getArticle(), $product->getInfo()->getName(), (int)$command->count);
        $client = new Client($command->name, $command->contact);
        $order = Order::create($client, $productOrder);
        $this->orders->add($order);
        $this->flusher->flush($order);
    }
}
