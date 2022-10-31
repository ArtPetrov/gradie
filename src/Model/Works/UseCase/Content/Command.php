<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Content;

use App\Model\Works\Entity\Content;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $header;

    public $content;

    public $price;

    public static function fromWork(Content $content): self
    {
        $command = new self();
        $command->header = $content->getHeader();
        $command->content = $content->getContent();
        $command->name = $content->getName();
        $command->price = $content->getPrice();
        return $command;
    }
}
