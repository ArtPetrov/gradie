<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Move;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public $direction;

    public function __construct(int $id, ?string $direction = '')
    {
        $this->id = $id;
        $this->direction = $direction;
    }
}
