<?php

declare(strict_types=1);

namespace App\Model\Article\UseCase\Edit;

use App\Model\Article\Repository\ArticleRepository;
use App\Model\File\Repository;
use App\Model\Article\Entity\Image;
use App\Model\Article\Entity\Article;
use App\Model\Article\Entity\Seo;
use App\Model\Article\Entity\Name;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $articles;
    private $files;
    private $tmpFiles;

    public function __construct(Flusher $flusher, ArticleRepository $articles, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles)
    {
        $this->flusher = $flusher;
        $this->articles = $articles;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
    }

    public function handle(Command $command): void
    {
        $news = $this->articles->get($command->id);

        $news->setContent($command->content);
        $news->setPublishedAt($command->publishedAt);

        $name = new Name(
            $command->name->full,
            $command->name->short
        );
        $news->changeName($name);

        $seo = new Seo(
            $command->seo->title,
            $command->seo->keywords,
            $command->seo->description,
        );
        $news->updateSeo($seo);

        $this->images($command, $news);

        $this->flusher->flush($news);
    }


    private function images(Command $command, Article $news)
    {
        $flagCover = 0;
        $currentImages = array_flip(array_map(static function (Image $rec): int {
            return $rec->getFile()->getId();
        }, $news->getImages()));

        foreach ($command->images as $image) {
            $flagCover += (int)$image->cover;
            /** @var Image $img */
            foreach ($news->getImages() as $img) {
                if ($img->getFile()->getId() === (int)$image->id) {
                    if ($img->getPosition() !== (int)$image->position) {
                        $img->setPosition((int)$image->position);
                    }
                    if ($img->isCover() !== (bool)$image->cover) {
                        $img->setCover((bool)$image->cover);
                    }
                    unset($currentImages[(int)$image->id]);
                    continue 2;
                }
            }
            $file = $this->files->get((int)$image->id);
            $news->addImage(new Image(
                $news,
                $this->files->get((int)$image->id),
                (bool)$image->cover,
                (int)$image->position
            ));
            $this->tmpFiles->removeByFile($file);
        }
        /** @var Image $available */
        foreach ($news->getImages() as $available) {
            if (array_key_exists($available->getFile()->getId(), $currentImages)) {
                $news->removeImage($available);
            }
        }

        if (0 < count($command->images) && 0 === $flagCover) {
            throw new \DomainException('article.not.set.cover');
        }
    }
}