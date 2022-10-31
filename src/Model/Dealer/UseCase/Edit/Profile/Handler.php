<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Edit\Profile;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Repository\DealerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $dealers;
    private $em;

    public function __construct(DealerRepository $dealers, EntityManagerInterface $em)
    {
        $this->dealers = $dealers;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        if ($this->dealers->duplicateEmail($command->email, $command->id)) {
            throw new \DomainException('email.duplicate');
        }

        /** @var Dealer $dealer */
        $dealer = $this->dealers->findOneBy(['id' => $command->id]);

        $dealer->changeEmail($command->email);

//        $dealer->info()
//            ->changeName($command->name)
//            ->changePhone($command->phone)
//            ->changeAddresss($command->address)
//            ->changeSite($command->site)
//            ->changeInn($command->inn)
//            ->changeKpp($command->kpp);

        if ($command->notification === true) {
            $dealer->notification()->subscribe();
        } else {
            $dealer->notification()->unsubscribe();
        }

        $this->em->persist($dealer);
        $this->em->flush();
    }
}
