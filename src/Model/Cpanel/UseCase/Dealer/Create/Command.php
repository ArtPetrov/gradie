<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    public $manager;

    public $category;

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

    public $notification = true;

}
