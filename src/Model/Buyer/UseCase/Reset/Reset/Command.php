<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Reset\Reset;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $token;

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

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
