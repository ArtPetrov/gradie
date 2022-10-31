<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Price
{
    /**
     * @ORM\Column(type="decimal", scale=2, nullable=false, options={"default":0.00, "unsigned"=true})
     */
    private $current;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true, options={"default":0.00, "unsigned"=true})
     */
    private $old;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $final;

    public function __construct(float $current, bool $final = true, ?float $old = null)
    {
        $this->current = $current;
        $this->final = $final;
        $this->old = $old;
    }

    public function getCurrent(): float
    {
        return (float) $this->current;
    }

    public function isFinalPrice(): bool
    {
        return $this->final;
    }

    public function getOld(): ?float
    {
        return (float) $this->old;
    }

}
