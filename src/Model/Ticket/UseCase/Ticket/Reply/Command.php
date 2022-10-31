<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Reply;

use App\Model\Cpanel\Entity\Administrator;
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
    public $support;
    public $ticket;

    public function __construct(Administrator $administrator, Ticket $ticket)
    {
        $this->support = $administrator;
        $this->ticket = $ticket;

    }
}
