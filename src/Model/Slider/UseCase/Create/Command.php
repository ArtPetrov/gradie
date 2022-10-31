<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Create;

use App\Model\Slider\Entity\Type;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $type;

    /**
     * @Assert\Valid()
     */
    public $info;

    /**
     * @Assert\Valid()
     */
    public $button;

    public $cover;

    public function __construct(Type $type)
    {
        $this->type = $type->getType();
    }
}

