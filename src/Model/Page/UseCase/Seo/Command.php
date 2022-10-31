<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Seo;

use App\Model\Page\Contract;
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

    public static function fromPage(Contract\Seo $seo): self
    {
        $command = new self();
        $command->title = $seo->getTitle();
        $command->keywords = $seo->getKeywords();
        $command->description = $seo->getDescription();
        return $command;
    }

}
