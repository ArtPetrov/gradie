<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Valid()
     */
    public $info;

    public $cover;
}
