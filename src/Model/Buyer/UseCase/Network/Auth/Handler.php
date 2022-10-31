<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Network\Auth;

use App\Model\Buyer\Entity\BasketToken;
use App\Model\Buyer\Entity\Buyer;
use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Buyer\Service\BasketTokenizer;
use App\Model\Flusher;

class Handler
{
    private $buyers;
    private $flusher;
    private $tokenizer;

    public function __construct(BuyerRepository $buyers, Flusher $flusher, BasketTokenizer $tokenizer)
    {
        $this->buyers = $buyers;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
    }

    public function handle(Command $command): void
    {
        if ($this->buyers->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new \DomainException('buyer.oauth.user.exists');
        }

        $buyer = Buyer::signUpByNetwork(
            $command->name,
            $command->network,
            $command->identity
        );

        $buyer->initBasketToken(new BasketToken($this->tokenizer->generate()->getToken()));

        $this->buyers->add($buyer);
        $this->flusher->flush();
    }
}
