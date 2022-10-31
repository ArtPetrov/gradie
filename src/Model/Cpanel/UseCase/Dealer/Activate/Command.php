<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Activate;

use App\Model\Dealer\Entity\Dealer;

class Command
{
    public $dealer;

    public $sendMail = false;
    public $generatePassword = false;

    public $category;
    public $manager;

    public function __construct(Dealer $dealer)
    {
        $this->dealer = $dealer;
    }
}
