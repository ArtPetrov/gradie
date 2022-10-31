<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Edit;

use App\Model\Dealer\Entity\Dealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    public $manager;

    public $category;

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


    public $contrahens;

    public $notification;

    public $company;
    public $city;
    public $leader;
    public $profile;
    public $why_we;
    public $how_know;
    public $experience;

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
        $command->notification = $dealer->notification()->approve();
        $command->contrahens = $dealer->info()->getContrahens();

        $command->company = $dealer->request()->getCompany();
        $command->city = $dealer->request()->getCity();
        $command->leader = $dealer->request()->getLeader();
        $command->profile = $dealer->request()->getProfile();
        $command->why_we = $dealer->request()->getWhyWe();
        $command->how_know = $dealer->request()->getHowKnow();
        $command->experience = $dealer->request()->getExperience();

        if ($category = $dealer->getCategory()) {
            $command->category = $category->getId();
        }

        if ($manager = $dealer->getManager()) {
            $command->manager = $manager->getId();
        }

        return $command;
    }
}
