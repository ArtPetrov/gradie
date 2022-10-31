<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Create;

use App\Model\Salon\UseCase\Coordinates;
use App\Model\Salon\UseCase\Information;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Valid()
     * @var Coordinates\Command
     */
    public $coords;

    /**
     * @Assert\Valid()
     * @var Information\Command
     */
    public $info;

    public $dealer;

    public $type;
}
