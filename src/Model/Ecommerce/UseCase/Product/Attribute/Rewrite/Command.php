<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute\Rewrite;

use Doctrine\Common\Collections\ArrayCollection;

class Command
{
    public $slug;
    public $oldValues;
    public $newValues;

    public function __construct(string $slug, ArrayCollection $oldValues, ArrayCollection $newValues)
    {
        $this->slug = $slug;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
    }
}
