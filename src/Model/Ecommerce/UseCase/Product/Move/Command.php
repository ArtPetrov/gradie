<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Move;

use App\Model\Ecommerce\Entity\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public $direction;

    public function __construct(Product $work, ?string $direction = '')
    {
        $this->id = $work->getId();
        $this->direction = $direction;
    }
}
