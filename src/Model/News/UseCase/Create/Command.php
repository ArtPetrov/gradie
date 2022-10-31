<?php

declare(strict_types=1);

namespace App\Model\News\UseCase\Create;

class Command
{
    public $header;
    public $content;
    public $publishedAt;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
    }
}
