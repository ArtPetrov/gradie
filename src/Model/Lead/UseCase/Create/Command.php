<?php

declare(strict_types=1);

namespace App\Model\Lead\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     */
    public $phone;

    /**
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     */
    public $city;

    /**
     * @Assert\NotBlank()
     */
    public $type;

    public function __construct(?string $type)
    {
        $this->type = $type;
    }
}

