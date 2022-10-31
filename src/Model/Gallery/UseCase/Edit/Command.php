<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Edit;

use App\Model\Gallery\Entity\Album;
use App\Model\Gallery\UseCase\Seo;
use App\Model\Gallery\UseCase\Name;
use App\Model\Gallery\UseCase\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

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

    public function __construct(Album $album)
    {
        $this->id = $album->getId();
        $this->seo = Seo\Command::fromAlbum($album->getSeo());
        $this->name = Name\Command::fromAlbum($album->getName());
        $this->images = new ArrayCollection(array_map(static function ($field) {
            return Image\Command::fromGallery($field);
        }, $album->getImages()));
    }
}
