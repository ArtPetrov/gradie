<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Edit\Profile;

use App\Model\Dealer\Entity\Dealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;

    /**
     * @Assert\Length(min="5")
     */
    public $site;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $phone;

    /**
     * @Assert\Length(min="5")
     */
    public $address;

    /**
     * @Assert\Length(min="10", max="12")
     */
    public $inn;

    public $kpp;

    public $contrahens;

    public $notification;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromDealer(Dealer $dealer): self
    {
        $command = new self($dealer->getId());
        $command->email = $dealer->getEmail();
        $command->name = $dealer->info()->getName();
        $command->site = $dealer->info()->getSite();
        $command->phone = $dealer->info()->getPhone();
        $command->address = $dealer->info()->getAddress();
        $command->inn = $dealer->info()->getInn();
        $command->kpp = $dealer->info()->getKpp();
        $command->contrahens = $dealer->info()->getContrahens();
        $command->notification = $dealer->notification()->approve();
        return $command;

    }
}
