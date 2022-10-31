<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\CategoryDealer\Edit;

use App\Model\Cpanel\Entity\CategoryDealer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */

    public $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function fromCategoryDealer(CategoryDealer $categoryDealer): self
    {
        $command = new self($categoryDealer->getId());
        $command->name = $categoryDealer->getName();
        return $command;
    }
}
