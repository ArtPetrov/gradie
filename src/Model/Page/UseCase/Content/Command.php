<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Content;

use App\Model\Page\Entity\Content;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $header;

    public $body;

    public static function fromPage(Content $content): self
    {
        $command = new self();
        $command->header = $content->getHeader();
        $command->body = $content->getBody();
        return $command;
    }
}
