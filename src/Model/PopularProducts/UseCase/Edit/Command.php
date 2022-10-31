<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Edit;

use App\Model\PopularProducts\Entity\PopularProducts;
use App\Model\PopularProducts\UseCase\Info;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

    /**
     * @Assert\Valid()
     */
    public $info;

    public $cover;
    public $prevCover;

    public function __construct(PopularProducts $product)
    {
        $this->id = $product->getId();
        $this->info = Info\Command::fromPopular($product);
        $this->prevCover = $product->getCover();
    }
}
