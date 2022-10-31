<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Edit;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\Cpanel\Repository\ManagerRepository;
use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Repository\DealerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var CategoryDealerRepository
     */
    private $categories;
    /**
     * @var ManagerRepository
     */
    private $managers;
    /**
     * @var DealerRepository
     */
    private $dealers;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        DealerRepository $dealers,
        CategoryDealerRepository $categories,
        ManagerRepository $managers,
        EntityManagerInterface $em)
    {
        $this->categories = $categories;
        $this->managers = $managers;
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

        if ($command->category) {
            $dealer->setCategory($this->categories->findOneBy(['id' => $command->category]));
        }
        if ($command->manager) {
            $dealer->assignManager($this->managers->findOneBy(['id' => $command->manager]));
        }
        $dealer->changeEmail($command->email);

        $dealer->info()
            ->changeName($command->name)
            ->changePhone($command->phone)
            ->changeAddresss($command->address)
            ->changeSite($command->site)
            ->changeInn($command->inn)
            ->changeKpp($command->kpp)
            ->changeContrahens($command->contrahens);

        if ($command->notification === true) {
            $dealer->notification()->subscribe();
        } else {
            $dealer->notification()->unsubscribe();
        }

        $this->em->persist($dealer);
        $this->em->flush();
    }
}
