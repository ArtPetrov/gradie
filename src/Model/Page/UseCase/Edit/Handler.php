<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Edit;

use App\Model\Page\Entity\Seo;
use App\Model\Page\Entity\Settings;
use App\Model\Page\Entity\Content;
use App\Model\Flusher;
use App\Model\Page\Repository\PageRepository;

class Handler
{
    private $flusher;
    private $pages;

    public function __construct(Flusher $flusher, PageRepository $pages)
    {
        $this->flusher = $flusher;
        $this->pages = $pages;
    }

    public function handle(Command $command): void
    {
        $seo = new Seo($command->seo->title, $command->seo->keywords, $command->seo->description);
        $content = new Content($command->content->header, $command->content->body);
        $settings = new Settings($command->settings->slug, $command->settings->template, $command->settings->status);

        $page = $this->pages->get($command->id);

        $page->changeName($command->name);
        $page->updateSeo($seo);
        $page->reloadSettings($settings);
        $page->updateContent($content);

        $this->flusher->flush($page);
    }
}