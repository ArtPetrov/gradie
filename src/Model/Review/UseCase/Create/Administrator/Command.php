<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Create\Administrator;

use App\Model\Ecommerce\Entity\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $product;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255,min=2)
     */
    public $name;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $rating = 5;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255,min=10)
     */
    public $message;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $createAt;

    public static function fromProduct(Product $product): self
    {
        $command = new  self();
        $command->product = $product->getId();
        $command->createAt = new \DateTime();
        return $command;
    }
}