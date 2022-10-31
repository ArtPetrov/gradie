<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Edit;

use App\Model\Page\Entity\Page;
use App\Model\Page\UseCase\Seo;
use App\Model\Page\UseCase\Settings;
use App\Model\Page\UseCase\Content;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    /**
     * @Assert\Valid()
     * @var Settings\Command
     */
    public $settings;

    /**
     * @Assert\Valid()
     * @var Content\Command
     */
    public $content;

    public function __construct(Page $page)
    {
        $this->id = $page->getId();
        $this->name = $page->getName();
        $this->seo = Seo\Command::fromPage($page->getSeo());
        $this->settings = Settings\Command::fromPage($page->getSettings());
        $this->content = Content\Command::fromPage($page->getContent());
    }
}
