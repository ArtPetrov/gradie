<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Create;

use App\Model\Page\UseCase\Seo;
use App\Model\Page\UseCase\Settings;
use App\Model\Page\UseCase\Content;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
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

    public function __construct()
    {
        $this->seo = new Seo\Command();
        $this->settings = new Settings\Command();
        $this->content = new Content\Command();
    }
}
