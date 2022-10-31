<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Delete;

use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;

class Handler
{
    private $products;
    private $flusher;

    public function __construct(ProductRepository $products, Flusher $flusher)
    {
        $this->products = $products;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get($command->id);
        $this->products->remove($product);
        $this->flusher->flush();
    }
}
