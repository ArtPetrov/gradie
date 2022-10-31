<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Edit;

use App\Model\Works\Entity\Work;
use App\Model\Works\UseCase\Seo;
use App\Model\Works\UseCase\Content;
use App\Model\Works\UseCase\Image;
use App\Model\Works\UseCase\Composition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

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

    public $images;
    public $diy;
    public $composition;

    public function __construct(Work $work)
    {
        $this->id = $work->getId();
        $this->seo = Seo\Command::fromWork($work->getSeo());
        $this->content = Content\Command::fromWork($work->getContent());

        $this->images = new ArrayCollection(array_map(static function ($field) {
            return Image\Command::fromWork($field);
        }, $work->getImages()));

        $this->diy = new ArrayCollection(array_map(static function ($field) {
            return Image\Diy\Command::fromWork($field);
        }, $work->getDiys()));

        $this->composition = new ArrayCollection(array_map(static function ($field) {
            return Composition\Command::fromWork($field);
        }, $work->getComposition()));
    }
}
