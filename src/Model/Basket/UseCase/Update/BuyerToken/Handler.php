<?php

declare(strict_types=1);

namespace App\Model\Basket\UseCase\Update\BuyerToken;

use App\Model\Buyer\Entity\BasketToken;
use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Buyer\Service\BasketTokenizer;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $buyers;
    private $tokenizer;

    public function __construct(Flusher $flusher, BuyerRepository $buyers, BasketTokenizer $tokenizer)
    {
        $this->flusher = $flusher;
        $this->buyers = $buyers;
        $this->tokenizer = $tokenizer;
    }

    public function handle(Command $command): void
    {
        $buyer = $this->buyers->get((int)$command->buyer);
        if (!$token = $command->token) {
            $token = $this->tokenizer->generate()->getToken();
        }
        $buyer->initBasketToken(new BasketToken($token));
        $this->flusher->flush();
    }
}
