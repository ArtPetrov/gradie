<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\CategoryDealer\MovePosition;

use App\Model\Cpanel\Entity\CategoryDealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $currentCategory;

    public $direction;

    public function __construct(CategoryDealer $currentCategory, ?string $direction)
    {
        $this->currentCategory = $currentCategory;
        $this->direction = $direction??'';
    }
}
