<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute\Remove;

class Command
{
    public $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }
}
