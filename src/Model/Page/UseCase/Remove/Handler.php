<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Page\Repository\PageRepository;

class Handler
{
    private $pages;
    private $flusher;

    public function __construct(PageRepository $pages, Flusher $flusher)
    {
        $this->pages = $pages;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $product = $this->pages->get($command->id);
        $this->pages->remove($product);
        $this->flusher->flush();
    }
}
