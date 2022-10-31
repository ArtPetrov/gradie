<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Coordinates;

use App\Model\Salon\Entity\Coordinates;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $lat;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $lon;

    public static function fromCoords(Coordinates $coordinates): self
    {
        $command = new self();
        $command->lat = $coordinates->getLat();
        $command->lon = $coordinates->getLon();
        return $command;
    }

}
