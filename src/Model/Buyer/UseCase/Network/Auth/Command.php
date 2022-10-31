<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Network\Auth;

class Command
{
    /**
     * @var string
     */
    public $network;
    /**
     * @var string
     */
    public $identity;
    /**
     * @var string
     */
    public $name;

    public function __construct(string $network, string $identity)
    {
        $this->network = $network;
        $this->identity = $identity;
    }
}
