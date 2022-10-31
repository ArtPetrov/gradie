<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Settings;

use App\Model\Page\Entity\Settings;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Length(max=255)
     */
    public $template;

    /**
     * @Assert\PositiveOrZero())
     */
    public $status = 200;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $slug;

    public static function fromPage(Settings $settings): self
    {
        $command = new self();
        $command->template = $settings->getTemplate();
        $command->status = $settings->getStatusCode();
        $command->slug = $settings->getSlug();
        return $command;
    }
}
