<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Setting\Password;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var int
     * @Assert\NotBlank()
     */
    public $id;

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

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
