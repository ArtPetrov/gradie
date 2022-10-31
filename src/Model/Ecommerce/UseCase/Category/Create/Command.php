<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Create;

use App\Model\Ecommerce\UseCase\Category\Seo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $name;

    public $type;

    public $parent;

    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    public $filters;

    public function __construct()
    {
        $this->filters = new ArrayCollection();
    }

}
