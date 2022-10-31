<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Move;

use App\Model\Works\Entity\Work;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public $direction;

    public function __construct(Work $work, ?string $direction = '')
    {
        $this->id = $work->getId();
        $this->direction = $direction;
    }
}
