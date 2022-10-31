<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Create\Administrator;

use App\Model\Review\Entity\Review;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;
use App\Model\Review\Repository\ReviewRepository;

class Handler
{
    private $flusher;
    private $reviews;
    private $products;

    public function __construct(Flusher $flusher, ReviewRepository $reviews, ProductRepository $products)
    {
        $this->flusher = $flusher;
        $this->reviews = $reviews;
        $this->products = $products;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get((int)$command->product);

        if(mb_strlen($command->name)<2){
            throw new \DomainException('review.name.incorrectly');
        }

        if(mb_strlen($command->message)<10){
            throw new \DomainException('review.message.incorrectly');
        }

        $review = Review::leftByAdministrator($product, $command->name, $command->message, $command->rating, $command->createAt);
        $this->reviews->add($review);
        $this->flusher->flush($review);
    }
}
