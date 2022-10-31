<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Move;

use App\Model\Cpanel\Entity\CategoryDealer;
use App\Model\Ecommerce\Entity\Category\Category;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public $parent = null;

    public $direction;

    public function __construct(Category $currentCategory, ?string $direction)
    {
        $this->id = $currentCategory->getId();

        if ($currentCategory->getParent() !== null) {
            $this->parent = $currentCategory->getParent()->getId();
        }

        $this->direction = $direction ?? '';
    }
}
