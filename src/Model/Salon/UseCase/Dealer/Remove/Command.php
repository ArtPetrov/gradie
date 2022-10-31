<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Remove;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Salon\Entity\Salon;

class Command
{
    public $dealer;
    public $salon;

    public function __construct(Dealer $dealer, Salon $salon)
    {
        $this->dealer = $dealer->getId();
        $this->salon  = $salon->getId();
    }
}
