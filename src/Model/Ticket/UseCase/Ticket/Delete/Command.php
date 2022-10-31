<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Delete;

use App\Model\Ticket\Entity\Ticket\Ticket;

class Command
{
    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
