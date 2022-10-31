<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\CategoryDealer\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;
}
