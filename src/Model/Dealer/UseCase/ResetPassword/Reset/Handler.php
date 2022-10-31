<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\ResetPassword\Reset;

use App\Model\Dealer\Repository\DealerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Handler
{
    private $dealers;
    private $hasher;
    private $em;

    public function __construct(DealerRepository $dealers, UserPasswordEncoderInterface $hasher, EntityManagerInterface $em)
    {
        $this->dealers = $dealers;
        $this->hasher = $hasher;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        if (!$dealer = $this->dealers->findByResetToken($command->token)) {
            throw new \DomainException('reset.token.invalid');
        }

        $dealer->passwordReset(
            new \DateTimeImmutable(),
            $this->hasher->encodePassword($dealer, $command->password)
        );

        if ($command->password !== $command->repeatPassword) {
            throw new \DomainException('passwords.not.equal');
        }

        $this->em->persist($dealer);
        $this->em->flush();
    }
}
