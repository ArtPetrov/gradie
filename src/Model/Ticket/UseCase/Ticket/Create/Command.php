<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Create;

use App\Model\Dealer\Entity\Dealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $header;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $content;

    public $file;

    public $author;

    public function __construct(Dealer $dealer)
    {
        $this->author = $dealer;
    }

}
