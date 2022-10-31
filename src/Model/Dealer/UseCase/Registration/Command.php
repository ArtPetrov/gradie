<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Registration;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $company;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $city;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $phone;

    public $leader;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $profile;

    public $site;
    public $why_we;
    public $how_know;
    public $experience;

    public $frod_a;
    public $frod_b;
    public $frod_result;

}
