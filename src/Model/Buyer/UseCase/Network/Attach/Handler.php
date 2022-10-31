<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Network\Attach;

use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $buyers;

    public function __construct(BuyerRepository $buyers, Flusher $flusher)
    {
        $this->flusher = $flusher;
        $this->buyers = $buyers;
    }

    public function handle(Command $command): void
    {
        if ($this->buyers->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new \DomainException('buyer.oauth.profile.already.use');
        }

        $buyer = $this->buyers->get($command->user);

        $buyer->attachNetwork(
            $command->network,
            $command->identity
        );

        $this->flusher->flush();
    }
}
