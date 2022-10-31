<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Setting\Information;

use App\Model\Buyer\Entity\Information;
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

        if ($buyer->getInformation()->getEmail() !== mb_strtolower($command->email)) {
            if ($this->buyers->hasByEmail(mb_strtolower($command->email))) {
                throw new \DomainException('buyer.email.user.exists');
            }
        }

        $buyer->updateInformation(new Information($command->email, $buyer->getPassword(), $command->name, $command->phone));

        $this->flusher->flush();
    }
}
