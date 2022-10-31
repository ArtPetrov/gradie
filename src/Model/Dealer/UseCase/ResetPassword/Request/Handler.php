<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\ResetPassword\Request;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Dealer\Service\ResetTokenizer;
use App\Model\Dealer\Service\ResetTokenSender;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $dealers;
    private $tokenizer;
    private $em;
    private $sender;

    public function __construct(
        DealerRepository $dealers,
        ResetTokenizer $tokenizer,
        EntityManagerInterface $em,
        ResetTokenSender $sender
    )
    {
        $this->dealers = $dealers;
        $this->tokenizer = $tokenizer;
        $this->em = $em;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        /** @var Dealer $dealer */
        $dealer = $this->dealers->findOneBy(['email' => $command->email]);

        if(!$dealer){
            throw new \DomainException('Username could not be found.');
        }

        $dealer->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );

        $this->em->persist($dealer);
        $this->em->flush();

        $this->sender->send($dealer->getEmail(), $dealer->getResetToken(), $dealer->info()->getName());
    }
}
