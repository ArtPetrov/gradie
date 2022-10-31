<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Filter\Rename;

class Command
{
    public $currentSlug;
    public $newSlug;

    public function __construct(string $currentSlug, string $newSlug)
    {
        $this->currentSlug = $currentSlug;
        $this->newSlug = $newSlug;
    }
}
