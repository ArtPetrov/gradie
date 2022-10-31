<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Seo;

use App\Model\Ecommerce\Contract;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @Assert\Length(max=255)
     */
    public $title;

    /**
     * @Assert\Length(max=255)
     */
    public $keywords;

    /**
     * @Assert\Length(max=255)
     */
    public $description;

    public static function fromCategorySeo(Contract\Seo $seo): self
    {
        $command = new self();
        $command->title = $seo->getTitle();
        $command->keywords = $seo->getKeywords();
        $command->description = $seo->getDescription();
        return $command;
    }

}
