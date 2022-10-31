<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Info;

use App\Model\PopularProducts\Entity\PopularProducts;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $header;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $link;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=12)
     */
    public $price;

    public static function fromPopular(PopularProducts $product): self
    {
        $command = new self();
        $command->header = $product->getHeader();
        $command->link = $product->getLink();
        $command->price = $product->getPrice();
        return $command;
    }
}
