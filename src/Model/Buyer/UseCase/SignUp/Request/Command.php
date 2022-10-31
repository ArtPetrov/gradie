<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\SignUp\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $phone;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $password;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $repeatPassword;

    /**
     * @var int
     */
    public $a;
    /**
     * @var int
     */
    public $b;
    /**
     * @var int
     */
    public $c;

    public function __construct()
    {
        $this->a = mt_rand(0, 100);
        $this->b = mt_rand(0, 100);
    }
}
