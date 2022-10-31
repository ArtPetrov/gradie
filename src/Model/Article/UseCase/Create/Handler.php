<?php

declare(strict_types=1);

namespace App\Model\Article\UseCase\Create;

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
        $seo = new Seo($command->seo->title, $command->seo->keywords, $command->seo->description);
        $name = new Name($command->name->full, $command->name->short);

        $news = Article::create($name, $command->content, $command->publishedAt, $seo);
        $this->articles->add($news);

        $coverFlag = 0;
        foreach ($command->images->getValues() as $image) {
            $file = $this->files->get((int)$image->id);
            $coverFlag += (int)$image->cover;
            $img = new Image(
                $news,
                $file,
                (bool)$image->cover,
                (int)$image->position
            );
            $news->addImage($img);
            $this->tmpFiles->removeByFile($file);
        }

        if ($coverFlag === 0 && $command->images->count() > 0) {
            throw new \DomainException('article.not.set.cover');
        }

        $this->flusher->flush($news);
    }
}