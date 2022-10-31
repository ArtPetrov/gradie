<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Move;

use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $products;

    public function __construct(Flusher $flusher, ProductRepository $products)
    {
        $this->flusher = $flusher;
        $this->products = $products;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get($command->id);
        $currentRating = $product->getPopular();
        $friendlyProductId = $this->products->findByPopular($product->getId(), $currentRating, $command->direction);

        if ($friendlyProductId === 0) {
            throw new \DomainException('position.extreme');
        }

        $friendlyProduct = $this->products->get($friendlyProductId);
        $friendlyRating = $friendlyProduct->getPopular();

        if ($friendlyRating === $currentRating) {
            $friendlyRating = ($command->direction === 'up') ? --$friendlyRating : ++$friendlyRating;
        }

        $product->updatePopular($friendlyRating);
        $friendlyProduct->updatePopular($currentRating);

        $this->flusher->flush();
    }
}
