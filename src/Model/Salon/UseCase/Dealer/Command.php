<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer;

use App\Model\Salon\Entity\Owners;

class Command
{
    public $id;
    public $name;
    public $dealer;

    public static function fromOwner(Owners $owners): self
    {
        $command = new self();
        $command->id = $owners->getDealer()->getId();
        $command->name = $owners->getDealer()->getShowName();
        $command->dealer = $owners->getDealer();
        return $command;
    }
}
