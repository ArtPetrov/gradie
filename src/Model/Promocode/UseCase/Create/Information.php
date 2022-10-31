<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Information
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $code;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;

    /**
     * @Assert\Length(min="5")
     */
    public $description;
}
