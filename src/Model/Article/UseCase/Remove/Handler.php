<?php

declare(strict_types=1);

namespace App\Model\Article\UseCase\Remove;

use App\Model\Article\Repository\ArticleRepository;
use App\Model\Flusher;

class Handler
{
    private $articles;
    private $flusher;

    public function __construct(ArticleRepository $articles, Flusher $flusher)
    {
        $this->articles = $articles;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $news = $this->articles->get($command->id);
        $this->articles->remove($news);
        $this->flusher->flush();
    }
}
