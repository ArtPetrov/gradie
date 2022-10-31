<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Assign;

class Command
{

    public $dealer;
    public $salon;

    public function __construct(int $dealer, int $salon)
    {
        $this->dealer = $dealer;
        $this->salon  = $salon;
    }
}
