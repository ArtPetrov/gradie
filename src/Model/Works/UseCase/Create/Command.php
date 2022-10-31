<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Create;

use App\Model\Works\UseCase\Seo;
use App\Model\Works\UseCase\Content;
use App\Model\Works\UseCase\Composition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Valid()
     * @var Content\Command
     */
    public $content;

    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    /**
     * @Assert\Valid()
     * @var Composition\Command
     */
    public $composition;

    public $images;
    public $diy;

    public function __construct()
    {
        $this->seo = new Seo\Command();
        $this->content = new Content\Command();
        $this->images = new ArrayCollection();
        $this->diy = new ArrayCollection();
        $this->composition = new ArrayCollection();
    }
}
