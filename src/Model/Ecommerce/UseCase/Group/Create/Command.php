<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Create;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    public $selectors;
    public $products;

    public function __construct()
    {
        $this->selectors = new ArrayCollection();
        $this->products = new ArrayCollection();
    }
}
