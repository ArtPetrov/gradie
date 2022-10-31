<?php

declare(strict_types=1);

namespace App\Model\Article\UseCase\Edit;

use App\Model\Article\Entity\Article;
use App\Model\Article\UseCase\Seo;
use App\Model\Article\UseCase\Name;
use App\Model\Article\UseCase\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

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

    public function __construct(Article $news)
    {
        $this->id = $news->getId();
        $this->publishedAt = $news->getPublishedAt();
        $this->content = $news->getContent();
        $this->seo = Seo\Command::fromArticle($news->getSeo());
        $this->name = Name\Command::fromArticle($news->getName());
        $this->images = new ArrayCollection(array_map(static function ($field) {
            return Image\Command::fromArticle($field);
        }, $news->getImages()));
    }
}
