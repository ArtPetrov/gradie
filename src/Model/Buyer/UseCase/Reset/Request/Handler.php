<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Reset\Request;

use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Buyer\Service\ResetTokenizer;
use App\Model\Buyer\Service\ResetTokenSender;
use App\Model\Flusher;

class Handler
{
    private $buyers;
    private $tokenizer;
    private $flusher;
    private $sender;

    public function __construct(
        BuyerRepository $buyers,
        ResetTokenizer $tokenizer,
        Flusher $flusher,
        ResetTokenSender $sender
    )
    {
        $this->buyers = $buyers;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $buyer = $this->buyers->getByEmail($command->email);

        $buyer->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );

        $this->flusher->flush();

        $this->sender->send($buyer->getInformation()->getEmail(), $buyer->getResetToken());
    }
}
