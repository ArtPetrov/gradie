<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Create;

use App\Model\Cpanel\UseCase\Dealer\Activate;
use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Repository\DealerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var DealerRepository
     */
    private $dealers;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Activate\Handler
     */
    private $activate;

    public function __construct(DealerRepository $dealers, EntityManagerInterface $em, Activate\Handler $activate)
    {
        $this->dealers = $dealers;
        $this->em = $em;
        $this->activate = $activate;
    }

    public function handle(Command $command): void
    {
        if ($this->dealers->duplicateEmail($command->email)) {
            throw new \DomainException('email.duplicate');
        }

        $dealer = Dealer::create($command->email);

        $dealer->info()
            ->changeName($command->name)
            ->changePhone($command->phone)
            ->changeAddresss($command->address)
            ->changeSite($command->site)
            ->changeInn($command->inn)
            ->changeContrahens($command->contrahens)
            ->changeKpp($command->kpp);

        if ($command->notification === true) {
            $dealer->notification()->subscribe();
        } else {
            $dealer->notification()->unsubscribe();
        }

        $this->em->persist($dealer);

        $activation = new Activate\Command($dealer);
        $activation->sendMail = true;
        $activation->generatePassword = true;
        $activation->category = $command->category;
        $activation->manager = $command->manager;
        $this->activate->handle($activation);

        $this->em->flush();
    }
}
