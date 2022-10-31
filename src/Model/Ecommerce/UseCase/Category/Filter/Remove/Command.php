<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Filter\Remove;

class Command
{
    public $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }
}
