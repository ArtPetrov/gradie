<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Name;

use App\Model\Gallery\Entity\Name;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $short;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $full;

    public static function fromAlbum(Name $name): self
    {
        $command = new self();
        $command->short = $name->getShort();
        $command->full = $name->getFull();
        return $command;
    }
}
