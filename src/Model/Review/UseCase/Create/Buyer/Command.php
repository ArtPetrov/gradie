<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Create\Buyer;

use App\Model\Buyer\Entity\Buyer;

class Command
{
    public $product;
    public $buyer = null;
    public $name;
    public $rating = 5;
    public $message;

    private function __construct($product, $name, $rating, $message)
    {
        $this->product = $product;
        $this->name = $name;
        $this->rating = $rating;
        $this->message = $message;
    }

    public static function formBuyer(Buyer $buyer, $product, $rating, $message): self
    {
        $command = new  self($product, $buyer->getInformation()->getName(), $rating, $message);
        $command->buyer = $buyer->getId();
        return $command;
    }

    public static function fromGuest($product, $name, $rating, $message): self
    {
        return new  self($product, $name, $rating, $message);
    }
}