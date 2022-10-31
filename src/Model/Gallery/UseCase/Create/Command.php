<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Create;

use App\Model\Gallery\UseCase\Seo;
use App\Model\Gallery\UseCase\Name;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Valid()
     * @var Name\Command
     */
    public $name;

    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    public $images;

    public function __construct()
    {
        $this->seo = new Seo\Command();
        $this->name = new Name\Command();
        $this->images = new ArrayCollection();
    }
}
