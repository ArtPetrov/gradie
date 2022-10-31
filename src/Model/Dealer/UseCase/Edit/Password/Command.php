<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Edit\Password;

use App\Model\Dealer\Entity\Dealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Length(min=6)
     */
    public $password;

    /**
     * @Assert\Length(min=6)
     */
    public $repeatPassword;

    public $currentDealer;

    public function __construct(Dealer $currentDealer)
    {
        $this->currentDealer = $currentDealer;
    }
}
