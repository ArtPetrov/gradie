<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Seo;

use App\Model\Ecommerce\Entity\Category\Seo;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $title;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $keywords;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $description;

    public $content;

    public static function fromCategorySeo(Seo $seo): self
    {
        $command = new self();
        $command->title = $seo->getTitle();
        $command->keywords = $seo->getKeywords();
        $command->description = $seo->getDescription();
        $command->content = $seo->getContent();
        return $command;
    }

}
