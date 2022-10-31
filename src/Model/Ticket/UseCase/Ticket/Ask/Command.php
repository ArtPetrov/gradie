<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Ask;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Ticket\Entity\Ticket\Ticket;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $content;

    public $file;
    public $author;
    public $ticket;

    public function __construct(Dealer $dealer, Ticket $ticket)
    {
        $this->author = $dealer;
        $this->ticket = $ticket;
    }
}
