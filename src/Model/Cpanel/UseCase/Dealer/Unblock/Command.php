<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Unblock;

use App\Model\Dealer\Entity\Dealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $dealer;

    public function __construct(Dealer $dealer)
    {
        $this->dealer = $dealer;
    }
}
