<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Cancel\Remove;

class Command
{
    public $salon;

    public function __construct(int $salon)
    {
        $this->salon  = $salon;
    }
}
