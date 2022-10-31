<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Registration;

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
        if ($command->frod_result !== $command->frod_a * $command->frod_b && (int)$command->frod_result < 1) {
            throw new \DomainException('javascript.not.support');
        }

        if ($this->dealers->duplicateEmail($command->email)) {
            throw new \DomainException('email.duplicate');
        }

        $dealer = Dealer::create($command->email);
        $dealer->info()
            ->changeName($command->name)
            ->changeSite($command->site)
            ->changePhone($command->phone);

        $dealer->request()
            ->setCompany($command->company)
            ->setCity($command->city)
            ->setLeader($command->leader)
            ->setProfile($command->profile)
            ->setWhyWe($command->why_we)
            ->setHowKnow($command->how_know)
            ->setExperience($command->experience);

        $this->em->persist($dealer);
        $this->em->flush();
    }
}
