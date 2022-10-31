<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Category;

use Symfony\Component\Validator\Constraints as Assert;

class Filter
{

    /**
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    public $slug;

    /**
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    public $type;

    /**
     * @Assert\NotNull()
     */
    public $label;

    /**
     * @Assert\NotNull()
     * @Assert\Type("integer")
     */
    public $position;

    public static function fromCategory(string $slug, string $type, ?string $label,int $position): self
    {
        $attribute = new self();
        $attribute->slug = $slug;
        $attribute->type = $type;
        $attribute->label = $label;
        $attribute->position = $position;
        return $attribute;
    }

}
