<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Setting\Password;

use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Flusher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Handler
{
    private $buyers;
    private $hasher;
    private $flusher;

    public function __construct(BuyerRepository $buyers, UserPasswordEncoderInterface $hasher, Flusher $flusher)
    {
        $this->buyers = $buyers;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $buyer = $this->buyers->get($command->id);

        if ($command->password !== $command->repeatPassword) {
            throw new \DomainException('buyer.passwords.not.equal');
        }

        $buyer->getInformation()->changePassword( $this->hasher->encodePassword($buyer, $command->password));

        $this->flusher->flush();
    }
}
