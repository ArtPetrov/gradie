<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Close;

use App\Model\Cpanel\Entity\Administrator;
use App\Model\Dealer\Entity\Dealer;
use App\Model\Ticket\Entity\Ticket\Ticket;

class Command
{

    public $author;
    public $support;
    public $ticket;

    private function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public static function forDealer(Ticket $ticket, Dealer $dealer): self
    {
        $command = new self($ticket);
        $command->author = $dealer;
        return $command;
    }

    public static function forSupport(Ticket $ticket, Administrator $administrator): self
    {
        $command = new self($ticket);
        $command->support = $administrator;
        return $command;
    }

}
