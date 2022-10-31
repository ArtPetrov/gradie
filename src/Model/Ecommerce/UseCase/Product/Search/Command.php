<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Search;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $query;

}
