<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

class Volume
{
    private $current;
    private $final;

    public function __construct(float $current, bool $final = true)
    {
        $this->current = $current;
        $this->final = $final;
    }

    public function getValue(): float
    {
        return $this->current;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

}