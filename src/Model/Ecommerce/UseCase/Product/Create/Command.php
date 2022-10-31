<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Create;

use App\Model\Ecommerce\UseCase\Product\Seo;
use App\Model\Ecommerce\UseCase\Product\Information;
use Doctrine\Common\Collections\ArrayCollection;
use App\Model\Ecommerce\UseCase\Product\Category;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    public $popular = 0;

    /**
     * @Assert\Valid()
     * @var Information\Command
     */
    public $information;

    public $images;

    public $attributes;
    public $composition;
    public $recommended;
    public $categories;

    public function __construct()
    {
        $this->information = new Information\Command();
        $this->seo = new Seo\Command();

        $this->attributes = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->composition = new ArrayCollection();
        $this->recommended = new ArrayCollection();

        $this->categories = new Category\Command();
    }
}
