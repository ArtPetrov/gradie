<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Move;

use App\Model\Gallery\Entity\Album;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public $direction;

    public function __construct(Album $album, ?string $direction = '')
    {
        $this->id = $album->getId();
        $this->direction = $direction;
    }
}
