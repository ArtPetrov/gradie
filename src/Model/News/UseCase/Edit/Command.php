<?php

declare(strict_types=1);

namespace App\Model\News\UseCase\Edit;

use App\Model\News\Entity\News;

class Command
{
    public $news;
    public $header;
    public $content;
    public $publishedAt;

    public function __construct(News $news)
    {
        $this->news = $news;
        $this->header = $news->getHeader();
        $this->content = $news->getContent();
        $this->publishedAt = $news->getPublishedAt();
    }
}
