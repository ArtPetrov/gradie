<?php

declare(strict_types=1);

namespace App\Model\Article\UseCase\Create;

use App\Model\Article\UseCase\Seo;
use App\Model\Article\UseCase\Name;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $content;

    public $publishedAt;

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
        $this->publishedAt = new \DateTimeImmutable();
        $this->seo = new Seo\Command();
        $this->name = new Name\Command();
        $this->images = new ArrayCollection();
    }
}
