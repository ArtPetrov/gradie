<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Edit\Password;

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
        if ($command->password !== $command->repeatPassword) {
            throw new \DomainException('passwords.not.equal');
        }

        $command->currentDealer->changePassword($this->hasher->encodePassword($command->currentDealer,$command->password));

        $this->em->persist($command->currentDealer);
        $this->em->flush();
    }
}
