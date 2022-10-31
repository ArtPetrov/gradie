<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use Symfony\Component\Validator\Constraints as Assert;

class Attribute
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
     * @Assert\Type("string")
     */
    public $label;

    /**
     * @Assert\NotNull()
     */
    public $value;

    /**
     * @Assert\NotNull()
     * @Assert\Type("integer")
     */
    public $position;

    /**
     * @Assert\Type("bool")
     */
    public $visible = false;

    public static function fromDatabase(string $slug, array $data): self
    {
        $attribute = new self();
        $attribute->slug = $slug;
        $attribute->type = $data['type'];
        $attribute->label = $data['label'];
        $attribute->value = $data['value'];
        $attribute->visible = $data['visible'];
        $attribute->position = $data['position'];
        return $attribute;
    }
}
