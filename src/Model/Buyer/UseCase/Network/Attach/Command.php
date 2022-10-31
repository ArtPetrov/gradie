<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Network\Attach;

class Command
{
    /**
     * @var int
     */
    public $user;
    /**
     * @var string
     */
    public $network;
    /**
     * @var string
     */
    public $identity;

    public function __construct(int $user, string $network, string $identity)
    {
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }
}
