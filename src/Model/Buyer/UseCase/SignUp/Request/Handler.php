<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\SignUp\Request;

use App\Model\Buyer\Entity\BasketToken;
use App\Model\Buyer\Entity\Buyer;
use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Buyer\Service\BasketTokenizer;
use App\Model\Flusher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Handler
{
    private $buyers;
    private $hasher;
    private $flusher;
    private $tokenizer;

    public function __construct(
        BuyerRepository $buyers,
        UserPasswordEncoderInterface $hasher,
        Flusher $flusher,
        BasketTokenizer $tokenizer
    )
    {
        $this->buyers = $buyers;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
    }

    public function handle(Command $command): void
    {
        if ($command->a * $command->b !== (int)$command->c) {
            throw new \DomainException('buyer.detected.spam');
        }

        if ($command->password !== $command->repeatPassword) {
            throw new \DomainException('buyer.passwords.not.equal');
        }

        if ($this->buyers->hasByEmail($command->email)) {
            throw new \DomainException('buyer.email.user.exists');
        }

        $buyer = Buyer::signUpByEmail(
            $command->email,
            null,
            $command->name,
            $command->phone
        );

        $buyer->getInformation()->changePassword($this->hasher->encodePassword($buyer, $command->password));

        $buyer->initBasketToken(new BasketToken($this->tokenizer->generate()->getToken()));

        $this->buyers->add($buyer);
        $this->flusher->flush();
    }
}
